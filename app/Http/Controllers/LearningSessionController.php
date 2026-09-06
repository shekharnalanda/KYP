<?php

namespace App\Http\Controllers;

use App\Models\ActivityRecord;
use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\LearningSession;
use App\Models\LearningSessionProgress;
use App\Services\CoursewareService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LearningSessionController extends Controller
{
    public function index(Request $request): View
    {
        $courses = Course::where('is_active', true)->orderBy('position')->with('sessions')->get();
        $completedIds = collect();
        $progressMap = collect();

        if ($request->user()->hasRole('student')) {
            $completedIds = ActivityRecord::where('user_id', $request->user()->id)
                ->where('status', 'completed')
                ->pluck('learning_session_id');

            $progressMap = LearningSessionProgress::where('user_id', $request->user()->id)
                ->get()
                ->keyBy('learning_session_id');
        }

        return view('learning.index', compact('courses', 'completedIds', 'progressMap'));
    }

    public function show(Request $request, LearningSession $session, CoursewareService $coursewareService): View
    {
        $session->load('course');
        $this->ensureSequentialAccess($request, $session);

        $completed = false;
        $progress = null;
        $courseware = $coursewareService->for($session);
        $courseSessions = LearningSession::where('course_id', $session->course_id)
            ->orderBy('session_number')
            ->get();
        $courseProgressMap = collect();
        $completedSessionIds = collect();

        if ($request->user()->hasRole('student')) {
            $completedSessionIds = ActivityRecord::where('user_id', $request->user()->id)
                ->where('status', 'completed')
                ->whereIn('learning_session_id', $courseSessions->pluck('id'))
                ->pluck('learning_session_id');

            $courseProgressMap = LearningSessionProgress::where('user_id', $request->user()->id)
                ->whereIn('learning_session_id', $courseSessions->pluck('id'))
                ->get()
                ->keyBy('learning_session_id');

            $completed = $completedSessionIds->contains($session->id);
            $progress = $courseProgressMap->get($session->id) ?? LearningSessionProgress::firstOrCreate(
                ['user_id' => $request->user()->id, 'learning_session_id' => $session->id],
                ['last_activity_at' => now()]
            );
            $courseProgressMap->put($session->id, $progress);
        }

        return view('learning.show', compact(
            'session',
            'completed',
            'progress',
            'courseware',
            'courseSessions',
            'courseProgressMap',
            'completedSessionIds'
        ));
    }

    public function progress(Request $request, LearningSession $session, CoursewareService $coursewareService): JsonResponse
    {
        abort_unless($request->user()->hasRole('student'), 403);
        $this->ensureSequentialAccess($request, $session);

        $data = $request->validate([
            'current_step' => ['required', 'integer', 'min:0', 'max:30'],
            'active_seconds' => ['nullable', 'integer', 'min:0', 'max:30'],
            'completed_steps' => ['nullable', 'array', 'max:30'],
            'completed_steps.*' => ['string', 'max:60'],
            'quiz_answers' => ['nullable', 'array', 'max:30'],
            'quiz_answers.*' => ['string', 'max:500'],
            'activity_states' => ['nullable', 'array', 'max:30'],
            'activity_states.*' => ['boolean'],
            'practical_response' => ['nullable', 'string', 'max:8000'],
        ]);

        $courseware = $coursewareService->for($session);
        $allowedSteps = $coursewareService->stepIds($courseware);
        $allowedActivities = $coursewareService->activityIds($courseware);

        $progress = DB::transaction(function () use ($request, $session, $data, $courseware, $coursewareService, $allowedSteps, $allowedActivities) {
            $record = LearningSessionProgress::where('user_id', $request->user()->id)
                ->where('learning_session_id', $session->id)
                ->lockForUpdate()
                ->first();

            if (! $record) {
                $record = new LearningSessionProgress([
                    'user_id' => $request->user()->id,
                    'learning_session_id' => $session->id,
                ]);
            }

            $requestedCredit = (int) ($data['active_seconds'] ?? 0);
            $credit = min(30, $requestedCredit);
            if ($record->last_activity_at) {
                $elapsed = (int) $record->last_activity_at->diffInSeconds(now());
                $credit = min($credit, max(0, $elapsed + 3));
            }

            $steps = collect(array_merge($record->completed_steps ?? [], $data['completed_steps'] ?? []))
                ->unique()
                ->filter(fn ($step) => in_array($step, $allowedSteps, true))
                ->values()
                ->all();

            $answers = array_merge($record->quiz_answers ?? [], $data['quiz_answers'] ?? []);
            $activityStates = collect(array_merge($record->activity_states ?? [], $data['activity_states'] ?? []))
                ->filter(fn ($complete, $activityId) => $complete === true && in_array($activityId, $allowedActivities, true))
                ->map(fn () => true)
                ->all();
            $practical = array_key_exists('practical_response', $data)
                ? trim((string) $data['practical_response'])
                : $record->practical_response;

            $record->fill([
                'current_step' => min((int) $data['current_step'], max(0, count($allowedSteps) - 1)),
                'furthest_step' => max(
                    (int) $record->furthest_step,
                    min((int) $data['current_step'], max(0, count($allowedSteps) - 1))
                ),
                'completed_steps' => $steps,
                'active_seconds' => min(((int) $session->required_active_minutes) * 60, ((int) $record->active_seconds) + $credit),
                'quiz_answers' => $answers,
                'activity_states' => $activityStates,
                'quiz_score' => $coursewareService->score($courseware, $answers),
                'practical_response' => $practical,
                'practical_submitted_at' => filled($practical) ? ($record->practical_submitted_at ?? now()) : null,
                'last_activity_at' => now(),
            ])->save();

            return $record->fresh();
        });

        return response()->json([
            'saved' => true,
            'active_seconds' => $progress->active_seconds,
            'quiz_score' => (float) ($progress->quiz_score ?? 0),
            'activity_states' => $progress->activity_states ?? [],
            'completed_steps' => $progress->completed_steps ?? [],
        ]);
    }

    public function complete(Request $request, LearningSession $session, CoursewareService $coursewareService): RedirectResponse
    {
        abort_unless($request->user()->hasRole('student'), 403);
        $this->ensureSequentialAccess($request, $session);

        $courseware = $coursewareService->for($session);
        $requiredSteps = $coursewareService->stepIds($courseware);
        $requiredActivities = $coursewareService->activityIds($courseware);
        $progress = LearningSessionProgress::where('user_id', $request->user()->id)
            ->where('learning_session_id', $session->id)
            ->firstOrFail();

        $missingSteps = array_diff($requiredSteps, $progress->completed_steps ?? []);
        $missingActivities = array_diff($requiredActivities, array_keys(array_filter($progress->activity_states ?? [])));
        $requiredSeconds = ((int) $session->required_active_minutes) * 60;

        if ($progress->active_seconds < $requiredSeconds || $missingSteps || $missingActivities || blank($progress->practical_response) || (float) $progress->quiz_score < (float) $session->passing_score) {
            throw ValidationException::withMessages([
                'session' => sprintf(
                    'Session अभी complete नहीं है: active time %d/%d मिनट, steps %d/%d, activities %d/%d, quiz %.0f/%d और practical submission आवश्यक है।',
                    intdiv((int) $progress->active_seconds, 60),
                    (int) $session->required_active_minutes,
                    count($requiredSteps) - count($missingSteps),
                    count($requiredSteps),
                    count($requiredActivities) - count($missingActivities),
                    count($requiredActivities),
                    (float) $progress->quiz_score,
                    (int) $session->passing_score
                ),
            ]);
        }

        DB::transaction(function () use ($request, $session, $progress): void {
            $progress->update(['completed_at' => now()]);

            ActivityRecord::updateOrCreate(
                ['user_id' => $request->user()->id, 'learning_session_id' => $session->id],
                ['status' => 'completed', 'score' => $progress->quiz_score, 'started_at' => $progress->created_at, 'completed_at' => now(), 'metadata' => [
                    'source' => 'interactive_courseware',
                    'active_seconds' => $progress->active_seconds,
                    'completed_steps' => $progress->completed_steps,
                ]]
            );

            // Hybrid KYP Lab Attendance:
            // Genuine interactive courseware completion may be credited as
            // Online Lab when no completed Centre-Iris Lab record exists.
            $physicalLab = AttendanceRecord::query()
                ->where('user_id', $request->user()->id)
                ->where('learning_session_id', $session->id)
                ->where('mode', 'lab')
                ->where('source', 'centre_iris')
                ->where('status', 'completed')
                ->exists();

            if (! $physicalLab) {
                AttendanceRecord::updateOrCreate(
                    [
                        'user_id' => $request->user()->id,
                        'learning_session_id' => $session->id,
                        'mode' => 'online_lab',
                    ],
                    [
                        'course_id' => $session->course_id,
                        'attendance_date' => now()->toDateString(),
                        'source' => 'online_portal',
                        'status' => 'completed',
                        'minutes_completed' => min(
                            (int) $session->duration_minutes,
                            intdiv((int) $progress->active_seconds, 60)
                        ),
                        'checked_in_at' => $progress->created_at,
                        'checked_out_at' => now(),
                        'checkout_source' => 'courseware_completion',
                        'recorded_by' => null,
                        'biometric_reference' => null,
                    ]
                );
            }
        });

        $next = LearningSession::where('course_id', $session->course_id)
            ->where('session_number', $session->session_number + 1)
            ->first();

        return $next
            ? redirect()->route('learning.show', $next)->with('status', 'Session complete—अगला session unlock हो गया है।')
            : redirect()->route('learning.index')->with('status', 'Course के सभी sessions complete हो गए।');
    }

    private function ensureSequentialAccess(Request $request, LearningSession $session): void
    {
        if (! $request->user()->hasRole('student')) {
            return;
        }

        $previous = LearningSession::where('course_id', $session->course_id)
            ->where('session_number', '<', $session->session_number)
            ->orderByDesc('session_number')
            ->first();

        if ($previous) {
            $previousCompleted = ActivityRecord::where('user_id', $request->user()->id)
                ->where('learning_session_id', $previous->id)
                ->where('status', 'completed')
                ->exists();

            abort_unless($previousCompleted, 403, 'Previous session must be completed first.');
        }
    }
}
