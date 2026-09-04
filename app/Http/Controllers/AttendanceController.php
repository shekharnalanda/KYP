<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\LearningSession;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $courses = Course::where('is_active', true)->orderBy('position')->with('sessions')->get();
        $students = User::where('role', 'student')->where('status', 'active')->orderBy('name')->get(['id', 'name', 'student_id']);
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

        $sessionBelongsToCourse = LearningSession::whereKey($validated['learning_session_id'])
            ->where('course_id', $validated['course_id'])
            ->exists();
        abort_unless($sessionBelongsToCourse, 422, 'Selected session does not belong to the course.');

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
}
