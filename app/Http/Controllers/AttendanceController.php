<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\LearningSession;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $courses = Course::where('is_active', true)->orderBy('position')->with('sessions')->get();
        $students = User::query()
            ->where('role', 'student')
            ->where('status', 'active')
            ->with(['enrollments' => fn ($query) => $query->where('status', 'active')])
            ->orderBy('name')
            ->get(['id', 'name', 'student_id']);
        $recent = AttendanceRecord::with(['user', 'course', 'learningSession', 'recorder'])->latest()->limit(25)->get();

        return view('attendance.index', compact('courses', 'students', 'recent'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'student')->where('status', 'active'))],
            'course_id' => ['required', 'exists:courses,id'],
            'learning_session_id' => ['required', 'exists:learning_sessions,id'],
            'attendance_date' => ['required', 'date', 'before_or_equal:today'],
            'mode' => ['required', Rule::in(['classroom', 'lab'])],
            'status' => ['required', Rule::in(['present', 'completed', 'absent'])],
            'minutes_completed' => ['required', 'integer', 'min:0', 'max:120'],
            'biometric_reference' => ['nullable', 'string', 'max:255'],
        ]);

        $this->validateSessionCourse((int) $validated['learning_session_id'], (int) $validated['course_id']);

        AttendanceRecord::updateOrCreate(
            [
                'user_id' => $validated['user_id'],
                'learning_session_id' => $validated['learning_session_id'],
                'mode' => $validated['mode'],
            ],
            $validated + ['recorded_by' => $request->user()->id]
        );

        return back()->with('status', 'Attendance सुरक्षित कर दी गई है।');
    }

    public function bulkStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'learning_session_id' => ['required', 'exists:learning_sessions,id'],
            'attendance_date' => ['required', 'date', 'before_or_equal:today'],
            'mode' => ['required', Rule::in(['classroom', 'lab'])],
            'records' => ['required', 'array', 'min:1', 'max:500'],
            'records.*.user_id' => ['required', 'integer', 'distinct', 'exists:users,id'],
            'records.*.status' => ['required', Rule::in(['present', 'completed', 'absent'])],
            'records.*.minutes_completed' => ['required', 'integer', 'min:0', 'max:120'],
        ]);

        $courseId = (int) $validated['course_id'];
        $this->validateSessionCourse((int) $validated['learning_session_id'], $courseId);

        $userIds = collect($validated['records'])->pluck('user_id')->map(fn ($id) => (int) $id);
        $validUserIds = User::query()
            ->whereIn('id', $userIds)
            ->where('role', 'student')
            ->where('status', 'active')
            ->whereHas('enrollments', fn ($query) => $query->where('course_id', $courseId)->where('status', 'active'))
            ->pluck('id');

        abort_unless($validUserIds->count() === $userIds->unique()->count(), 422, 'Only active enrolled students can be marked.');

        DB::transaction(function () use ($validated, $request): void {
            foreach ($validated['records'] as $record) {
                AttendanceRecord::updateOrCreate(
                    [
                        'user_id' => $record['user_id'],
                        'learning_session_id' => $validated['learning_session_id'],
                        'mode' => $validated['mode'],
                    ],
                    [
                        'course_id' => $validated['course_id'],
                        'attendance_date' => $validated['attendance_date'],
                        'status' => $record['status'],
                        'minutes_completed' => $record['status'] === 'absent' ? 0 : $record['minutes_completed'],
                        'recorded_by' => $request->user()->id,
                    ]
                );
            }
        });

        return back()->with('status', count($validated['records']).' students की bulk attendance सुरक्षित कर दी गई है।');
    }

    private function validateSessionCourse(int $sessionId, int $courseId): void
    {
        $valid = LearningSession::whereKey($sessionId)->where('course_id', $courseId)->exists();
        abort_unless($valid, 422, 'Selected session does not belong to the course.');
    }
}
