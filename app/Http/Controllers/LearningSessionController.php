<?php

namespace App\Http\Controllers;

use App\Models\ActivityRecord;
use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\LearningSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LearningSessionController extends Controller
{
    public function index(Request $request): View
    {
        $courses = Course::where('is_active', true)->orderBy('position')->with('sessions')->get();
        $completedIds = $request->user()->hasRole('student')
            ? ActivityRecord::where('user_id', $request->user()->id)->where('status', 'completed')->pluck('learning_session_id')
            : collect();

        return view('learning.index', compact('courses', 'completedIds'));
    }

    public function show(Request $request, LearningSession $session): View
    {
        $session->load('course');
        $completed = false;

        if ($request->user()->hasRole('student')) {
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

            $completed = ActivityRecord::where('user_id', $request->user()->id)
                ->where('learning_session_id', $session->id)
                ->where('status', 'completed')
                ->exists();
        }

        return view('learning.show', compact('session', 'completed'));
    }

    public function complete(Request $request, LearningSession $session): RedirectResponse
    {
        abort_unless($request->user()->hasRole('student'), 403);

        DB::transaction(function () use ($request, $session): void {
            ActivityRecord::updateOrCreate(
                ['user_id' => $request->user()->id, 'learning_session_id' => $session->id],
                ['status' => 'completed', 'score' => 100, 'started_at' => now(), 'completed_at' => now(), 'metadata' => ['source' => 'student_lab']]
            );

            AttendanceRecord::updateOrCreate(
                ['user_id' => $request->user()->id, 'learning_session_id' => $session->id, 'mode' => 'lab'],
                ['course_id' => $session->course_id, 'attendance_date' => today(), 'status' => 'completed', 'minutes_completed' => $session->duration_minutes]
            );
        });

        $next = LearningSession::where('course_id', $session->course_id)->where('session_number', $session->session_number + 1)->first();

        return $next
            ? redirect()->route('learning.show', $next)->with('status', 'Session complete—अगला session unlock हो गया है।')
            : redirect()->route('learning.index')->with('status', 'Course के सभी sessions complete हो गए।');
    }
}
