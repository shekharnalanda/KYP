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
                $questions = $steps->flatMap(fn (array $step) => $step['interactions'] ?? [])->values();
                $activities = $steps->pluck('activity')->filter()->values();
                $bilingualActivities = $activities->every(fn (array $activity) =>
                    filled($activity['title_hi'] ?? null)
                    && filled($activity['title_en'] ?? null)
                    && filled($activity['instruction_hi'] ?? null)
                    && filled($activity['instruction_en'] ?? null)
                    && count($activity['items'] ?? []) >= 4
                    && collect($activity['items'])->every(fn (array $item) => filled($item['hi'] ?? null) && filled($item['en'] ?? null))
                );
                $bilingual = $questions->every(fn (array $question) =>
                    filled($question['prompt_hi'] ?? null)
                    && filled($question['prompt_en'] ?? null)
                    && count($question['options'] ?? []) === 4
                    && collect($question['options'])->every(fn (array $option) => filled($option['hi'] ?? null) && filled($option['en'] ?? null))
                );

                if ($steps->count() !== 10 || $minutes !== 120 || $questions->count() < 10 || ! $bilingual || $activities->count() !== 10 || ! $bilingualActivities || ! $steps->contains('type', 'practical')) {
                    throw new \RuntimeException("Invalid courseware generated for {$session->course->code}-{$session->session_number}");
                }

                $session->update([
                    'courseware' => $courseware,
                    'required_active_minutes' => 120,
                    'passing_score' => 60,
                    'objectives_hi' => collect($courseware['outcomes'])->map(fn ($item, $index) => ($index + 1).'. '.$item)->implode("\n"),
                    'lesson_content_hi' => collect($steps)->whereIn('type', ['concept', 'demo'])->pluck('content')->implode("\n\n"),
                    'classroom_notes_hi' => "10-screen interactive lesson चलाएँ। Demonstration के बाद guided practice कराएँ, हर screen के bilingual checkpoint पर चर्चा करें और learner का independent evidence verify करें।",
                    'lab_activity_hi' => (string) $steps->firstWhere('type', 'practical')['content'],
                    'assessment_prompt_hi' => "दस topic-specific bilingual checkpoints, practical evidence और reflection पूरा करें। Minimum score 60% तथा 120 active minutes आवश्यक हैं।",
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
            'activities' => $items->sum(fn ($item) => collect($item->courseware['steps'] ?? [])->whereNotNull('activity')->count()),
            'questions' => $items->sum(fn ($item) => collect($item->courseware['steps'] ?? [])->sum(fn (array $step) => count($step['interactions'] ?? []))),
            'minutes' => $items->sum(fn ($item) => collect($item->courseware['steps'] ?? [])->sum('minutes')),
        ]);

        $this->table(['Course', 'Sessions', 'Authored', 'Activities', 'Questions', 'Minutes'], $counts->map(fn ($data, $code) => [$code, ...array_values($data)])->values()->all());
        $this->info("Updated {$updated} sessions. Detailed courseware is available for ".LearningSession::whereNotNull('courseware')->count().' sessions.');

        return self::SUCCESS;
    }
}
