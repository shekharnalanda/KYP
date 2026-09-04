<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\LearningSession;
use App\Models\Question;
use App\Models\Result;
use App\Models\User;
use App\Services\EligibilityService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function student(Request $request, EligibilityService $eligibility): View
    {
        return view('panels.student', [
            'summary' => $eligibility->summaryFor($request->user()),
        ]);
    }

    public function teacher(): View
    {
        return view('panels.teacher', [
            'courses' => Course::where('is_active', true)->orderBy('position')->withCount('sessions')->get(),
            'studentCount' => User::where('role', 'student')->where('status', 'active')->count(),
            'attendanceToday' => AttendanceRecord::whereDate('attendance_date', today())->count(),
        ]);
    }

    public function admin(): View
    {
        return view('panels.admin', [
            'stats' => [
                'students' => User::where('role', 'student')->count(),
                'teachers' => User::where('role', 'teacher')->count(),
                'courses' => Course::where('is_active', true)->count(),
                'sessions' => LearningSession::count(),
                'enrollments' => Enrollment::where('status', 'active')->count(),
                'attendance_today' => AttendanceRecord::whereDate('attendance_date', today())->count(),
                'exams' => Exam::count(),
                'questions' => Question::count(),
                'results' => Result::count(),
                'certificates' => Certificate::count(),
            ],
        ]);
    }
}
