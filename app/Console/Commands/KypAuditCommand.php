<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Exam;
use App\Models\LearningSession;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Services\ResultScoringService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class KypAuditCommand extends Command
{
    protected $signature = 'kyp:audit';
    protected $description = 'Run the KYP production readiness audit';

    public function handle(ResultScoringService $scoring): int
    {
        $checks = [];
        $checks[] = ['Environment', 'PHP 8.2 or newer', PHP_VERSION_ID >= 80200];
        $checks[] = ['Environment', 'Production mode', app()->environment('production')];
        $checks[] = ['Environment', 'Debug disabled', config('app.debug') === false];
        $checks[] = ['Environment', 'HTTPS application URL', str_starts_with((string) config('app.url'), 'https://')];
        $checks[] = ['Environment', 'Application key', filled(config('app.key'))];
        $checks[] = ['Environment', 'Writable storage', is_writable(storage_path())];
        $checks[] = ['Database', 'Learning tables', Schema::hasTable('courses') && Schema::hasTable('attendance_records') && Schema::hasTable('activity_records')];

        $lessonColumns = collect(DB::select("PRAGMA table_info('learning_sessions')"))->pluck('name');
        $checks[] = ['Database', 'Lesson content fields', collect(['objectives_hi', 'lesson_content_hi', 'classroom_notes_hi', 'lab_activity_hi'])->every(fn ($column) => $lessonColumns->contains($column))];

        $userColumns = collect(DB::select("PRAGMA table_info('users')"))->pluck('name');
        $checks[] = ['Database', 'User access fields', Schema::hasTable('enrollments') && collect(['phone', 'student_id', 'role', 'status'])->every(fn ($column) => $userColumns->contains($column))];

        $checks[] = ['Database', 'Assessment tables', Schema::hasTable('exams') && Schema::hasTable('questions') && Schema::hasTable('results') && Schema::hasTable('certificates')];
        $checks[] = ['Learning', 'Four courses', Course::count() === 4];
        $checks[] = ['Learning', '135 sessions', LearningSession::count() === 135];
        $checks[] = ['Learning', '135 published Hindi lessons', LearningSession::where('content_status', 'published')->whereNotNull('lesson_content_hi')->count() === 135];
        $authoredCourseware = LearningSession::whereNotNull('courseware')->get()->filter(function ($session): bool {
            $steps = collect($session->courseware['steps'] ?? []);
            $questions = $steps->flatMap(fn (array $step) => $step['interactions'] ?? []);

            return $steps->count() === 10
                && $steps->sum('minutes') === 120
                && $questions->count() >= 10
                && $questions->every(fn (array $question) =>
                    filled($question['prompt_hi'] ?? null)
                    && filled($question['prompt_en'] ?? null)
                    && count($question['options'] ?? []) === 4
                    && collect($question['options'])->every(fn (array $option) => filled($option['hi'] ?? null) && filled($option['en'] ?? null))
                );
        })->count();
        $checks[] = ['Learning', '135 bilingual 10-question interactive sessions', $authoredCourseware === 135];

        $thresholds = Course::whereIn('code', ['CIT', 'CLS', 'CSS', 'AI-DM'])->pluck('minimum_exam_sessions', 'code');
        $checks[] = ['Eligibility', 'Locked session thresholds', (int) $thresholds->get('CIT') === 48 && (int) $thresholds->get('CLS') === 32 && (int) $thresholds->get('CSS') === 16 && (int) $thresholds->get('AI-DM') === 12];

        $checks[] = ['Assessment', 'Published exams', Exam::where('status', 'published')->count() >= 4];
        $checks[] = ['Assessment', 'Bilingual questions', Question::whereNotNull('text_hi')->whereNotNull('text_en')->count() >= 20];
        $checks[] = ['Assessment', 'Question options', QuestionOption::count() >= 80];

        $score = $scoring->calculate(200, 200, 100);
        $checks[] = ['Scoring', '500 raw to 100 final', $score['total_raw'] === 500.0 && $score['final_score'] === 100.0];
        $checks[] = ['Scoring', 'Automatic pass mark', (float) config('kyp.scoring.pass_mark') === 40.0 && class_exists(\App\Services\InternalAssessmentService::class)];

        $checks[] = ['Operations', 'Backup and workflow commands', class_exists(\App\Console\Commands\KypBackupCommand::class) && class_exists(\App\Console\Commands\KypWorkflowTestCommand::class) && class_exists(\App\Console\Commands\BuildKypCoursewareCommand::class)];
        $checks[] = ['Security', 'Response security middleware', class_exists(\App\Http\Middleware\SecurityHeadersMiddleware::class)];

        $routeNames = collect(Route::getRoutes()->getRoutes())->pluck('action.as');
        $checks[] = ['Routes', 'Learning workflow', collect(['learning.index', 'learning.show', 'learning.progress', 'learning.complete'])->every(fn ($name) => $routeNames->contains($name))];
        $checks[] = ['Routes', 'Student exam workflow', collect(['student.exams', 'student.exam.start', 'student.exam.attempt', 'student.exam.submit', 'student.exam.result'])->every(fn ($name) => $routeNames->contains($name))];
        $checks[] = ['Routes', 'Attendance operations', collect(['attendance.index', 'attendance.store', 'attendance.bulk'])->every(fn ($name) => $routeNames->contains($name))];
        $checks[] = ['Routes', 'Result and certificate workflow', collect(['admin.results', 'admin.results.publish', 'admin.results.bulk', 'student.marksheet', 'student.certificate', 'certificate.verify'])->every(fn ($name) => $routeNames->contains($name))];
        $checks[] = ['Routes', 'Admin user operations', collect(['admin.users', 'admin.users.store', 'admin.users.status', 'admin.users.enrollments'])->every(fn ($name) => $routeNames->contains($name))];
        $checks[] = ['Routes', 'Admin progress reporting', $routeNames->contains('admin.progress')];
        $checks[] = ['Routes', 'Assessment CMS', collect(['admin.assessments', 'admin.exams.store', 'admin.exams.update', 'admin.questions.store', 'admin.questions.edit', 'admin.questions.update'])->every(fn ($name) => $routeNames->contains($name))];

        $rows = collect($checks)->map(fn (array $check) => [$check[0], $check[1], $check[2] ? 'PASS' : 'FAIL'])->all();
        $this->table(['Group', 'Check', 'Status'], $rows);

        $failed = collect($checks)->contains(fn (array $check) => ! $check[2]);
        $this->{$failed ? 'error' : 'info'}($failed ? 'KYP audit failed.' : 'KYP production audit passed.');

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
