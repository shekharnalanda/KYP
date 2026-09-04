<?php

namespace App\Console\Commands;

use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\EligibilityService;
use App\Services\InternalAssessmentService;
use App\Services\ResultScoringService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class KypWorkflowTestCommand extends Command
{
    protected $signature = 'kyp:workflow-test';
    protected $description = 'Run a rollback-safe KYP student workflow test';

    public function handle(EligibilityService $eligibility, InternalAssessmentService $internal, ResultScoringService $scoring): int
    {
        DB::beginTransaction();

        try {
            $course = Course::where('code', 'CIT')->with('sessions')->firstOrFail();
            $student = User::create([
                'name' => 'KYP Workflow Test',
                'email' => 'workflow-'.Str::uuid().'@example.invalid',
                'student_id' => 'TEST-'.Str::upper(Str::random(10)),
                'role' => 'student',
                'status' => 'active',
                'password' => Str::random(32),
            ]);

            Enrollment::create([
                'user_id' => $student->id,
                'course_id' => $course->id,
                'status' => 'active',
                'enrolled_at' => now(),
            ]);

            foreach ($course->sessions->take((int) $course->minimum_exam_sessions) as $session) {
                foreach (['lab', 'classroom'] as $mode) {
                    AttendanceRecord::create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                        'learning_session_id' => $session->id,
                        'recorded_by' => $student->id,
                        'attendance_date' => today(),
                        'mode' => $mode,
                        'status' => 'completed',
                        'minutes_completed' => 120,
                    ]);
                }
            }

            $summary = $eligibility->summaryFor($student)->first(fn (array $item) => $item['course']->id === $course->id);
            $raw = $internal->rawScores($student, $course);
            $final = $scoring->calculate(200, $raw['lab_raw'], $raw['classroom_raw']);

            $checks = [
                'enrolment' => $student->enrollments()->where('course_id', $course->id)->where('status', 'active')->exists(),
                'eligibility' => (bool) ($summary['eligible'] ?? false),
                'lab attendance' => (int) ($summary['lab_sessions'] ?? 0) === 48,
                'classroom attendance' => (int) ($summary['classroom_sessions'] ?? 0) === 48,
                '500-to-100 scoring' => $final['final_score'] === 88.0,
            ];

            foreach ($checks as $name => $passed) {
                $this->line(($passed ? 'PASS' : 'FAIL').' — '.$name);
            }

            return in_array(false, $checks, true) ? self::FAILURE : self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        } finally {
            DB::rollBack();
            $this->line('Temporary workflow data rolled back.');
        }
    }
}
