<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\User;

class InternalAssessmentService
{
    public function rawScores(User $user, Course $course): array
    {
        $totalSessions = max(1, (int) $course->total_sessions);

        $labSessions = $this->completedSessions($user, $course, 'lab');
        $classroomSessions = $this->completedSessions($user, $course, 'classroom');

        return [
            'lab_raw' => round(min(1, $labSessions / $totalSessions) * (float) config('kyp.scoring.lab.raw_max'), 2),
            'classroom_raw' => round(min(1, $classroomSessions / $totalSessions) * (float) config('kyp.scoring.classroom.raw_max'), 2),
        ];
    }

    private function completedSessions(User $user, Course $course, string $mode): int
    {
        return AttendanceRecord::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('mode', $mode)
            ->whereIn('status', ['present', 'completed'])
            ->distinct('learning_session_id')
            ->count('learning_session_id');
    }
}
