<?php

namespace App\Services;

use App\Models\LearningSession;
use Illuminate\Support\Str;

class CoursewareService
{
    public function for(LearningSession $session): array
    {
        if (is_array($session->courseware) && filled($session->courseware['steps'] ?? null)) {
            return $session->courseware;
        }

        $paragraphs = collect(preg_split('/\R+|(?<=[।.!?])\s+/u', (string) $session->lesson_content_hi))
            ->map(fn ($text) => trim((string) $text))
            ->filter()
            ->values();

        $midpoint = max(1, (int) ceil($paragraphs->count() / 2));
        $conceptOne = $paragraphs->take($midpoint)->implode("\n\n");
        $conceptTwo = $paragraphs->slice($midpoint)->implode("\n\n") ?: $conceptOne;
        $practical = trim((string) $session->lab_activity_hi);
        $correctOption = Str::limit($practical ?: 'दिए गए विषय का चरणबद्ध व्यावहारिक अभ्यास करना', 150);

        return [
            'version' => 1,
            'language' => 'hi',
            'steps' => [
                ['id' => 'orientation', 'minutes' => 10, 'type' => 'orientation', 'title' => 'लक्ष्य और तैयारी', 'content' => $session->objectives_hi],
                ['id' => 'concept-1', 'minutes' => 20, 'type' => 'concept', 'title' => 'Concept Learning — भाग 1', 'content' => $conceptOne],
                ['id' => 'concept-2', 'minutes' => 20, 'type' => 'concept', 'title' => 'Concept Learning — भाग 2', 'content' => $conceptTwo],
                ['id' => 'guided-demo', 'minutes' => 20, 'type' => 'demo', 'title' => 'Guided Demonstration', 'content' => $session->classroom_notes_hi],
                ['id' => 'knowledge-check', 'minutes' => 10, 'type' => 'quiz', 'title' => 'Knowledge Check', 'content' => $session->assessment_prompt_hi, 'interaction' => [
                    'id' => 'session-check-1',
                    'prompt' => 'इस session के topic से जुड़ी सही practical activity चुनें।',
                    'options' => [$correctOption, 'केवल page खुला छोड़ देना', 'बिना अभ्यास के session पूरा करना', 'विषय से असंबंधित कार्य करना'],
                    'correct' => '0',
                ]],
                ['id' => 'lab-practice', 'minutes' => 20, 'type' => 'practical', 'title' => 'स्वयं करें — Lab Activity', 'content' => $practical],
                ['id' => 'reflection', 'minutes' => 12, 'type' => 'reflection', 'title' => 'समझ और पुनरावृत्ति', 'content' => 'आज सीखे गए चरणों को दोहराएँ। अपनी कठिनाई, सीख और उपयोग का संक्षिप्त नोट practical box में लिखें।'],
                ['id' => 'final-checkpoint', 'minutes' => 8, 'type' => 'checkpoint', 'title' => 'Final Checkpoint', 'content' => 'सभी चरण, knowledge check और practical response पूरा करके session submit करें।'],
            ],
        ];
    }

    public function score(array $courseware, array $answers): float
    {
        $questions = collect($courseware['steps'] ?? [])->flatMap(
            fn (array $step) => $step['interactions'] ?? (isset($step['interaction']) ? [$step['interaction']] : [])
        );
        if ($questions->isEmpty()) {
            return 100.0;
        }

        $correct = $questions->filter(fn (array $question) =>
            array_key_exists($question['id'], $answers)
            && (string) $answers[$question['id']] === (string) $question['correct']
        )->count();

        return round(($correct / $questions->count()) * 100, 2);
    }

    public function stepIds(array $courseware): array
    {
        return collect($courseware['steps'] ?? [])->pluck('id')->filter()->values()->all();
    }
}
