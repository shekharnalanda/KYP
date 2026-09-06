<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Collection;

class EligibilityService
{
    public function summaryFor(User $user): Collection
    {
        return Course::query()->where('is_active', true)->orderBy('position')->get()->map(function (Course $course) use ($user): array {
            $labSessions = AttendanceRecord::query()
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->whereIn('mode', ['lab', 'online_lab'])
                ->where('status', 'completed')
                ->distinct('learning_session_id')
                ->count('learning_session_id');

            $classroomSessions = AttendanceRecord::query()
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('mode', 'classroom')
                ->whereIn('status', ['present', 'completed'])
                ->distinct('learning_session_id')
                ->count('learning_session_id');

            $required = $course->minimum_exam_sessions;

            return [
                'course' => $course,
                'lab_sessions' => $labSessions,
                'classroom_sessions' => $classroomSessions,
                'required_sessions' => $required,
                'eligible' => $required !== null && $labSessions >= $required,
                'progress' => min(100, (int) round(($labSessions / max(1, $course->total_sessions)) * 100)),
            ];
        });
    }
}
