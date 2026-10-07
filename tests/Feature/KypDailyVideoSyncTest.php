<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\LearningSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KypDailyVideoSyncTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'test-mci-daily-video-token';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:'.base64_encode(str_repeat('k', 32)),
            'services.mci_daily_video_sync.token' => self::TOKEN,
        ]);
    }

    public function test_preview_requires_the_shared_bearer_token(): void
    {
        $this->postJson('/api/mci/daily-videos/preview', ['videos' => $this->videos()])
            ->assertUnauthorized();

        $this->withToken('incorrect-token')
            ->postJson('/api/mci/daily-videos/preview', ['videos' => $this->videos()])
            ->assertUnauthorized();
    }

    public function test_preview_reports_all_135_sessions_in_course_order_without_changing_them(): void
    {
        $courses = $this->createCatalog();
        $videos = $this->videos();

        $response = $this->withToken(self::TOKEN)
            ->postJson('/api/mci/daily-videos/preview', ['videos' => $videos])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('days', 67)
            ->assertJsonPath('sessions', 135)
            ->assertJsonPath('already_linked', 0)
            ->assertJsonPath('will_change', 135);

        $mappings = collect($response->json('day_mappings'))->keyBy('day');
        $this->assertSame([
            ['course_code' => 'CIT', 'session_number' => 59],
            ['course_code' => 'CIT', 'session_number' => 60],
        ], $mappings->get(30)['sessions']);
        $this->assertSame([
            ['course_code' => 'CLS', 'session_number' => 1],
            ['course_code' => 'CLS', 'session_number' => 2],
        ], $mappings->get(31)['sessions']);
        $this->assertSame([
            ['course_code' => 'CLS', 'session_number' => 39],
            ['course_code' => 'CLS', 'session_number' => 40],
        ], $mappings->get(50)['sessions']);
        $this->assertSame([
            ['course_code' => 'CSS', 'session_number' => 1],
            ['course_code' => 'CSS', 'session_number' => 2],
        ], $mappings->get(51)['sessions']);
        $this->assertSame([
            ['course_code' => 'CSS', 'session_number' => 19],
            ['course_code' => 'CSS', 'session_number' => 20],
        ], $mappings->get(60)['sessions']);
        $this->assertSame([
            ['course_code' => 'AI-DM', 'session_number' => 1],
            ['course_code' => 'AI-DM', 'session_number' => 2],
        ], $mappings->get(61)['sessions']);
        $this->assertSame([
            ['course_code' => 'AI-DM', 'session_number' => 13],
            ['course_code' => 'AI-DM', 'session_number' => 14],
            ['course_code' => 'AI-DM', 'session_number' => 15],
        ], $mappings->get(67)['sessions']);

        $this->assertNotEmpty($response->json('structure_hash'));
        $this->assertDatabaseCount('learning_sessions', 135);
        $this->assertDatabaseMissing('learning_sessions', ['theory_youtube_video_id' => $videos[0]['youtube_video_id']]);
        $this->assertSame(4, count($courses));
    }

    public function test_sync_links_every_session_and_can_safely_reconcile_the_same_manifest(): void
    {
        $this->createCatalog();
        $videos = $this->videos();
        $preview = $this->withToken(self::TOKEN)
            ->postJson('/api/mci/daily-videos/preview', ['videos' => $videos])
            ->assertOk();

        $body = [
            'videos' => $videos,
            'expected_structure_hash' => $preview->json('structure_hash'),
        ];

        $this->withToken(self::TOKEN)
            ->putJson('/api/mci/daily-videos/sync', $body)
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('days_applied', 67)
            ->assertJsonPath('updated_sessions', 135)
            ->assertJsonPath('already_applied', false);

        $sessions = LearningSession::query()
            ->join('courses', 'courses.id', '=', 'learning_sessions.course_id')
            ->orderBy('courses.position')
            ->orderBy('courses.id')
            ->orderBy('learning_sessions.session_number')
            ->select('learning_sessions.*')
            ->get();

        $this->assertCount(135, $sessions);
        foreach ($sessions as $index => $session) {
            $ordinal = $index + 1;
            $day = $ordinal <= 132 ? intdiv($ordinal - 1, 2) + 1 : 67;
            $this->assertSame($day, (int) $session->theory_video_day);
            $this->assertSame($videos[$day - 1]['youtube_video_id'], $session->theory_youtube_video_id);
        }
        $this->assertSame('CIT session 1', $sessions->first()->title_en);
        $this->assertSame('published', $sessions->first()->content_status);

        $this->withToken(self::TOKEN)
            ->putJson('/api/mci/daily-videos/sync', $body)
            ->assertOk()
            ->assertJsonPath('already_applied', true)
            ->assertJsonPath('updated_sessions', 135);

        $this->withToken(self::TOKEN)
            ->postJson('/api/mci/daily-videos/preview', ['videos' => $videos])
            ->assertOk()
            ->assertJsonPath('already_linked', 135)
            ->assertJsonPath('will_change', 0);

        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);
        $courseByCode = Course::query()->get()->keyBy('code');
        foreach ([
            ['CIT', 1, 1],
            ['CIT', 2, 1],
            ['CIT', 3, 2],
            ['AI-DM', 13, 67],
            ['AI-DM', 14, 67],
            ['AI-DM', 15, 67],
        ] as [$code, $sessionNumber, $day]) {
            $session = LearningSession::query()
                ->where('course_id', $courseByCode->get($code)->id)
                ->where('session_number', $sessionNumber)
                ->firstOrFail();

            $this->actingAs($teacher)
                ->get(route('learning.show', $session))
                ->assertOk()
                ->assertSee('Day '.$day.' · दिन का थ्योरी वीडियो')
                ->assertSee('youtube-nocookie.com/embed/'.$videos[$day - 1]['youtube_video_id'], false);
        }
    }

    public function test_wrong_structure_hash_or_incomplete_manifest_never_changes_session_videos(): void
    {
        $this->createCatalog();
        $videos = $this->videos();

        $this->withToken(self::TOKEN)
            ->putJson('/api/mci/daily-videos/sync', [
                'videos' => $videos,
                'expected_structure_hash' => str_repeat('0', 64),
            ])
            ->assertStatus(409);

        $this->withToken(self::TOKEN)
            ->postJson('/api/mci/daily-videos/preview', ['videos' => array_slice($videos, 0, 66)])
            ->assertUnprocessable();

        $duplicateVideoIds = $videos;
        $duplicateVideoIds[1]['youtube_video_id'] = $duplicateVideoIds[0]['youtube_video_id'];
        $this->withToken(self::TOKEN)
            ->postJson('/api/mci/daily-videos/preview', ['videos' => $duplicateVideoIds])
            ->assertUnprocessable();

        $this->assertSame(0, LearningSession::query()->whereNotNull('theory_youtube_video_id')->count());
    }

    public function test_preview_stops_if_kyp_course_order_changes(): void
    {
        $this->createCatalog();
        Course::query()->where('code', 'CLS')->update(['position' => 0]);

        $this->withToken(self::TOKEN)
            ->postJson('/api/mci/daily-videos/preview', ['videos' => $this->videos()])
            ->assertStatus(409)
            ->assertJsonPath('ok', false);

        $this->assertSame(0, LearningSession::query()->whereNotNull('theory_youtube_video_id')->count());
    }

    public function test_preview_stops_if_a_course_session_number_is_missing(): void
    {
        $this->createCatalog();
        $cit = Course::query()->where('code', 'CIT')->firstOrFail();
        LearningSession::query()
            ->where('course_id', $cit->id)
            ->where('session_number', 60)
            ->update(['session_number' => 61]);

        $this->withToken(self::TOKEN)
            ->postJson('/api/mci/daily-videos/preview', ['videos' => $this->videos()])
            ->assertStatus(409)
            ->assertJsonPath('ok', false);

        $this->assertSame(0, LearningSession::query()->whereNotNull('theory_youtube_video_id')->count());
    }

    /** @return array<int, Course> */
    private function createCatalog(): array
    {
        $definitions = [
            ['CIT', 'Information Technology', 60, 1],
            ['CLS', 'Language Skills', 40, 2],
            ['CSS', 'Soft Skills', 20, 3],
            ['AI-DM', 'AI & Digital Marketing', 15, 4],
        ];

        return collect($definitions)->map(function (array $definition): Course {
            [$code, $name, $count, $position] = $definition;
            $course = Course::create([
                'code' => $code,
                'name' => $name,
                'total_sessions' => $count,
                'total_hours' => $count * 2,
                'position' => $position,
                'is_active' => true,
            ]);

            foreach (range(1, $count) as $number) {
                LearningSession::create([
                    'course_id' => $course->id,
                    'session_number' => $number,
                    'title_hi' => $code.' session '.$number,
                    'title_en' => $code.' session '.$number,
                    'assessment_prompt_hi' => 'Assessment '.$number,
                    'content_status' => 'published',
                    'published_at' => now(),
                ]);
            }

            return $course;
        })->all();
    }

    /** @return array<int, array{day:int, youtube_video_id:string}> */
    private function videos(): array
    {
        return collect(range(1, 67))->map(fn (int $day): array => [
            'day' => $day,
            'youtube_video_id' => 'Q'.str_pad((string) $day, 10, '0', STR_PAD_LEFT),
        ])->all();
    }
}
