<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\LearningSession;
use App\Services\CoursewareContentBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CitTroubleshootingProfileTest extends TestCase
{
    private function lesson(int $number, string $topic): array
    {
        $session = new LearningSession(['session_number' => $number, 'title_hi' => $topic]);
        $session->setRelation('course', new Course(['code' => 'CIT']));

        return app(CoursewareContentBuilder::class)->build($session);
    }

    public function test_troubleshooting_teaches_safe_diagnosis_instead_of_online_service_authentication(): void
    {
        $lesson = $this->lesson(57, 'Basic Troubleshooting');
        $text = implode("\n", array_column($lesson['steps'], 'content'));

        foreach (['error message', 'power', 'display input', 'Task Manager', 'diagnostic log', 'escalate'] as $term) {
            $this->assertStringContainsString($term, $text);
        }
        $this->assertStringContainsString('power supply न खोलें', $text);
        $this->assertStringContainsString('delete, format, registry edit या reset न करें', $text);
        $this->assertStringNotContainsString('MFA/secure method', $text);
        $this->assertStringNotContainsString('receipt/logout', $text);
        $this->assertSame(120, array_sum(array_column($lesson['steps'], 'minutes')));

        $questions = collect($lesson['steps'])->flatMap(fn (array $step) => $step['interactions'] ?? []);
        $process = $questions->firstWhere('id', 'CIT-57-process');
        $correct = $process['options'][(int) $process['correct']]['hi'];
        $this->assertStringContainsString('basic connections', $correct);
        $this->assertStringContainsString('teacher को escalate', $correct);

        $labels = array_column($lesson['steps'][0]['media']['cards'], 'icon');
        $this->assertContains('troubleshoot', $labels);
    }

    #[DataProvider('adjacentLessons')]
    public function test_neighbouring_topics_keep_their_correct_profile(int $number, string $topic, string $expected): void
    {
        $lesson = $this->lesson($number, $topic);
        $text = implode("\n", array_column($lesson['steps'], 'content'));
        $this->assertStringContainsString($expected, $text);
        $this->assertStringNotContainsString('simulated no-display', $text);
    }

    public static function adjacentLessons(): array
    {
        return [
            [56, 'DigiLocker और Digital Documents', 'receipt/logout'],
            [58, 'Integrated Practical Project', 'कम-से-कम तीन CIT skills'],
        ];
    }
}
