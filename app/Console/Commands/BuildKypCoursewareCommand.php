<?php

namespace App\Console\Commands;

use App\Models\LearningSession;
use App\Services\CoursewareContentBuilder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BuildKypCoursewareCommand extends Command
{
    protected $signature = 'kyp:courseware-build {--force : Rebuild sessions that already contain authored courseware}';
    protected $description = 'Build detailed 120-minute interactive courseware for all KYP sessions';

    public function handle(CoursewareContentBuilder $builder): int
    {
        $query = LearningSession::with('course')->orderBy('course_id')->orderBy('session_number');

        if (! $this->option('force')) {
            $query->whereNull('courseware');
        }

        $sessions = $query->get();
        $updated = 0;

        DB::transaction(function () use ($sessions, $builder, &$updated): void {
            foreach ($sessions as $session) {
                $courseware = $builder->build($session);
                $steps = collect($courseware['steps'] ?? []);
                $minutes = (int) $steps->sum('minutes');
                $questions = $steps->where('type', 'quiz')->filter(fn ($step) => filled($step['interaction'] ?? null))->count();

                if ($steps->count() !== 10 || $minutes !== 120 || $questions !== 3 || ! $steps->contains('type', 'practical')) {
                    throw new \RuntimeException("Invalid courseware generated for {$session->course->code}-{$session->session_number}");
                }

                $session->update([
                    'courseware' => $courseware,
                    'required_active_minutes' => 120,
                    'passing_score' => 60,
                    'objectives_hi' => collect($courseware['outcomes'])->map(fn ($item, $index) => ($index + 1).'. '.$item)->implode("\n"),
                    'lesson_content_hi' => collect($steps)->whereIn('type', ['concept', 'demo'])->pluck('content')->implode("\n\n"),
                    'classroom_notes_hi' => "10-screen interactive lesson चलाएँ। Demonstration के बाद guided practice कराएँ, तीन checkpoints पर चर्चा करें और learner का independent evidence verify करें।",
                    'lab_activity_hi' => (string) $steps->firstWhere('type', 'practical')['content'],
                    'assessment_prompt_hi' => "तीन topic-specific checkpoints, practical evidence और reflection पूरा करें। Minimum score 60% तथा 120 active minutes आवश्यक हैं।",
                    'duration_minutes' => 120,
                    'content_status' => 'published',
                    'published_at' => $session->published_at ?? now(),
                ]);
                $updated++;
            }
        });

        $counts = LearningSession::with('course')->get()->groupBy('course.code')->map(fn ($items) => [
            'sessions' => $items->count(),
            'authored' => $items->filter(fn ($item) => is_array($item->courseware) && count($item->courseware['steps'] ?? []) === 10)->count(),
            'questions' => $items->sum(fn ($item) => collect($item->courseware['steps'] ?? [])->where('type', 'quiz')->count()),
            'minutes' => $items->sum(fn ($item) => collect($item->courseware['steps'] ?? [])->sum('minutes')),
        ]);

        $this->table(['Course', 'Sessions', 'Authored', 'Questions', 'Minutes'], $counts->map(fn ($data, $code) => [$code, ...array_values($data)])->values()->all());
        $this->info("Updated {$updated} sessions. Detailed courseware is available for ".LearningSession::whereNotNull('courseware')->count().' sessions.');

        return self::SUCCESS;
    }
}
