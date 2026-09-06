<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\IrisAttendanceEvent;
use App\Models\IrisProfile;
use App\Models\LearningSession;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IrisConnectorController extends Controller
{
    public function health(Request $request): JsonResponse
    {
        $this->authorizeConnector($request);

        return response()->json([
            'ok' => true,
            'service' => 'KYP Iris Connector API',
            'device_model' => 'Mantra MIS100V2',
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function candidates(Request $request): JsonResponse
    {
        $this->authorizeConnector($request);

        $profiles = IrisProfile::query()
            ->where('is_active', true)
            ->whereHas('user', fn ($query) => $query->where('role', 'student')->where('status', 'active'))
            ->with('user:id,name,student_id')
            ->get()
            ->map(fn (IrisProfile $profile) => [
                'user_id' => $profile->user_id,
                'student_id' => $profile->user->student_id,
                'student_name' => $profile->user->name,
                'left_template' => $profile->left_template,
                'right_template' => $profile->right_template,
                'updated_at' => $profile->updated_at?->toIso8601String(),
            ]);

        return response()->json(['data' => $profiles]);
    }

    public function catalog(Request $request): JsonResponse
    {
        $this->authorizeConnector($request);

        $courses = Course::query()
            ->where('is_active', true)
            ->with(['sessions' => fn ($query) => $query
                ->whereNotNull('published_at')
                ->orderBy('session_number')])
            ->orderBy('position')
            ->get()
            ->map(fn (Course $course) => [
                'id' => $course->id,
                'code' => $course->code,
                'name' => $course->name,
                'sessions' => $course->sessions->map(fn (LearningSession $session) => [
                    'id' => $session->id,
                    'session_number' => $session->session_number,
                    'title_hi' => $session->title_hi,
                    'title_en' => $session->title_en,
                    'duration_minutes' => $session->duration_minutes,
                ])->values(),
            ]);

        return response()->json(['data' => $courses]);
    }

    public function students(Request $request): JsonResponse
    {
        $this->authorizeConnector($request);

        $students = User::query()
            ->where('role', 'student')
            ->where('status', 'active')
            ->whereHas('enrollments', fn ($query) => $query->where('status', 'active'))
            ->withExists(['irisProfile as iris_enrolled' => fn ($query) => $query->where('is_active', true)])
            ->orderBy('name')
            ->get(['id', 'name', 'student_id'])
            ->map(fn (User $student) => [
                'id' => $student->id,
                'student_id' => $student->student_id,
                'name' => $student->name,
                'iris_enrolled' => (bool) $student->iris_enrolled,
            ]);

        return response()->json(['data' => $students]);
    }

    public function enroll(Request $request): JsonResponse
    {
        $this->authorizeConnector($request);

        $validated = $request->validate([
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(
                    fn ($query) => $query->where('role', 'student')->where('status', 'active')
                ),
            ],
            'left_template' => ['nullable', 'string', 'max:2097152'],
            'right_template' => ['nullable', 'string', 'max:2097152'],
            'quality_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'device_reference' => ['nullable', 'string', 'max:255'],
        ]);

        abort_unless(
            filled($validated['left_template'] ?? null) || filled($validated['right_template'] ?? null),
            422,
            'At least one iris template is required.'
        );

        $profile = IrisProfile::updateOrCreate(
            ['user_id' => $validated['user_id']],
            [
                'left_template' => $validated['left_template'] ?? null,
                'right_template' => $validated['right_template'] ?? null,
                'quality_score' => $validated['quality_score'] ?? null,
                'device_model' => 'Mantra MIS100V2',
                'device_reference' => $validated['device_reference'] ?? null,
                'enrolled_at' => now(),
                'is_active' => true,
            ]
        );

        return response()->json([
            'ok' => true,
            'profile_id' => $profile->id,
            'message' => 'Iris enrollment सुरक्षित हो गया है।',
        ]);
    }

    public function attendance(Request $request): JsonResponse
    {
        $this->authorizeConnector($request);

        $validated = $request->validate([
            'event_uuid' => ['required', 'uuid'],
            'user_id' => [
                'required',
                Rule::exists('users', 'id')->where(
                    fn ($query) => $query->where('role', 'student')->where('status', 'active')
                ),
            ],
            'course_id' => ['required', 'exists:courses,id'],
            'learning_session_id' => ['required', 'exists:learning_sessions,id'],
            'event_type' => ['required', Rule::in(['check_in', 'check_out'])],
            'device_reference' => ['required', 'string', 'max:255'],
            'matched_eye' => ['nullable', Rule::in(['left', 'right'])],
            'match_score' => ['nullable', 'numeric', 'min:0'],
            'captured_at' => ['required', 'date', 'before_or_equal:'.now()->addMinutes(5)->toDateTimeString()],
            'metadata' => ['nullable', 'array'],
        ]);

        $capturedAt = Carbon::parse($validated['captured_at']);
        abort_if($capturedAt->lt(now()->subDays(7)), 422, 'Offline iris event is older than seven days.');

        $session = LearningSession::whereKey($validated['learning_session_id'])
            ->where('course_id', $validated['course_id'])
            ->firstOrFail();

        $student = User::query()
            ->whereKey($validated['user_id'])
            ->whereHas('enrollments', fn ($query) => $query
                ->where('course_id', $validated['course_id'])
                ->where('status', 'active'))
            ->firstOrFail();

        abort_unless(
            IrisProfile::where('user_id', $student->id)
                ->where('is_active', true)
                ->exists(),
            422,
            'Student iris enrollment is required before biometric attendance.'
        );

        $result = DB::transaction(function () use ($validated, $capturedAt, $session, $student): array {
            $existing = IrisAttendanceEvent::where('event_uuid', $validated['event_uuid'])->first();
            if ($existing) {
                return ['event' => $existing, 'duplicate' => true];
            }

            $event = IrisAttendanceEvent::create($validated + ['captured_at' => $capturedAt]);

            $attendance = AttendanceRecord::firstOrNew([
                'user_id' => $student->id,
                'learning_session_id' => $session->id,
                'mode' => 'lab',
            ]);

            $attendance->fill([
                'course_id' => $session->course_id,
                'attendance_date' => $capturedAt->toDateString(),
                'recorded_by' => null,
                'biometric_reference' => 'iris:'.$validated['device_reference'],
            ]);

            if ($validated['event_type'] === 'check_in') {
                $attendance->status = 'present';
                $attendance->minutes_completed = 0;
                $attendance->source = 'centre_iris';
                $attendance->checked_in_at = $capturedAt;
                $attendance->checked_out_at = null;
                $attendance->checkout_source = null;
            } else {
                $checkIn = IrisAttendanceEvent::query()
                    ->where('user_id', $student->id)
                    ->where('learning_session_id', $session->id)
                    ->where('event_type', 'check_in')
                    ->whereDate('captured_at', $capturedAt->toDateString())
                    ->where('captured_at', '<=', $capturedAt)
                    ->latest('captured_at')
                    ->first();

                abort_unless($checkIn, 422, 'Check-in is required before check-out.');

                $minutes = min(
                    (int) $session->duration_minutes,
                    max(0, $checkIn->captured_at->diffInMinutes($capturedAt))
                );
                $attendance->minutes_completed = $minutes;
                $attendance->source = 'centre_iris';
                $attendance->checked_in_at = $checkIn->captured_at;
                $attendance->checked_out_at = $capturedAt;
                $attendance->checkout_source = 'iris';
                $attendance->status = $minutes >= (int) config('iris.minimum_session_minutes', 110)
                    ? 'completed'
                    : 'present';
            }

            $attendance->save();

            return ['event' => $event, 'attendance' => $attendance, 'duplicate' => false];
        });

        return response()->json([
            'ok' => true,
            'duplicate' => $result['duplicate'],
            'student' => [
                'id' => $student->id,
                'student_id' => $student->student_id,
                'name' => $student->name,
            ],
            'event_type' => $validated['event_type'],
            'status' => $result['attendance']->status ?? 'already_synced',
            'minutes_completed' => $result['attendance']->minutes_completed ?? null,
            'message' => $result['duplicate']
                ? 'यह iris event पहले sync हो चुका है।'
                : 'Iris attendance सफलतापूर्वक दर्ज हो गई है।',
        ]);
    }

    private function authorizeConnector(Request $request): void
    {
        $expected = (string) config('iris.connector_token');
        $provided = (string) $request->bearerToken();

        abort_unless(
            $expected !== '' && $provided !== '' && hash_equals($expected, $provided),
            401,
            'Invalid iris connector token.'
        );
    }
}
