<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\LearningSession;
use App\Services\CoursewareContentBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CoursewareContentBuilderTest extends TestCase
{
    #[DataProvider('courseProvider')]
    public function test_builder_creates_complete_topic_specific_120_minute_courseware(string $code, int $number, string $topic): void
    {
        $course = new Course(['code' => $code, 'name' => $code]);
        $session = new LearningSession(['session_number' => $number, 'title_hi' => $topic]);
        $session->setRelation('course', $course);

        $courseware = app(CoursewareContentBuilder::class)->build($session);
        $steps = collect($courseware['steps']);

        $this->assertSame(3, $courseware['version']);
        $this->assertCount(10, $steps);
        $this->assertSame(120, $steps->sum('minutes'));
        $questions = $steps->flatMap(fn (array $step) => $step['interactions'] ?? []);
        $this->assertCount(10, $questions);
        $this->assertTrue($questions->every(fn (array $question) =>
            filled($question['prompt_hi'])
            && filled($question['prompt_en'])
            && collect($question['options'])->every(fn (array $option) => filled($option['hi']) && filled($option['en']))
        ));
        $this->assertTrue($steps->contains('type', 'practical'));
        $this->assertStringContainsString($topic, $steps->first()['content']);
        $this->assertCount(3, $courseware['outcomes']);
    }

    public static function courseProvider(): array
    {
        return [
            ['CIT', 30, 'Basic Formulas'],
            ['CLS', 29, 'Professional Email Writing'],
            ['CSS', 16, 'Interview Readiness'],
            ['AI-DM', 13, 'SEO की मूल बातें'],
        ];
    }
}
