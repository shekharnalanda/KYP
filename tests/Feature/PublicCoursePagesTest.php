<?php

namespace Tests\Feature;

use App\Models\Course;
use Database\Seeders\CourseStructureSeeder;
use Database\Seeders\SessionContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCoursePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([CourseStructureSeeder::class, SessionContentSeeder::class]);
    }

    public function test_homepage_explains_the_program_and_links_to_all_four_public_modules(): void
    {
        $response = $this->get('/')->assertOk()
            ->assertSee('KYP क्या है?')->assertSee('270')->assertSee('135')
            ->assertSee('दैनिक अध्ययन का सुझाया लक्ष्य')
            ->assertSee('Email को OTP से Verify करें।')
            ->assertSee(route('admission.form'), false)
            ->assertSee(route('enquiry.form'), false)
            ->assertSee(route('login'), false);

        foreach (['cit', 'cls', 'css', 'ai-dm'] as $slug) {
            $response->assertSee(route('public.courses.show', $slug), false);
        }
        Course::where('code', 'CIT')->update(['total_hours' => 122, 'total_sessions' => 61]);
        $this->get('/')->assertOk()->assertSee('272')->assertSee('136');
    }

    public function test_guests_can_read_each_modules_actual_published_session_list(): void
    {
        foreach (['cit' => 'CIT', 'cls' => 'CLS', 'css' => 'CSS', 'ai-dm' => 'AI-DM'] as $slug => $code) {
            $course = Course::where('code', $code)->firstOrFail();
            $response = $this->get(route('public.courses.show', $slug))->assertOk()
                ->assertSee($course->name)->assertSee((string) $course->total_hours)
                ->assertSee('आप क्या कर सकेंगे?')->assertSee('विषयवार पाठ्यक्रम')
                ->assertSee('सीधे होम पेज पर लौटें');
            foreach ($course->sessions as $session) {
                $response->assertSee($session->title_hi);
            }
        }
    }

    public function test_unpublished_and_future_sessions_are_not_exposed_in_public_lists(): void
    {
        $course = Course::where('code', 'CIT')->firstOrFail();
        $course->sessions()->where('session_number', 1)->update(['title_hi' => 'PRIVATE_DRAFT_TITLE', 'content_status' => 'draft']);
        $course->sessions()->where('session_number', 2)->update(['title_hi' => 'FUTURE_SESSION_TITLE', 'published_at' => now()->addDay()]);
        $course->sessions()->where('session_number', 3)->update(['title_hi' => 'UNSCHEDULED_SESSION_TITLE', 'published_at' => null]);
        $course->sessions()->where('session_number', 4)->update(['title_hi' => '<script>untrustedTitle()</script>']);

        $this->get('/courses/cit')->assertOk()
            ->assertDontSee('PRIVATE_DRAFT_TITLE')->assertDontSee('FUTURE_SESSION_TITLE')
            ->assertDontSee('UNSCHEDULED_SESSION_TITLE')
            ->assertDontSee('<script>untrustedTitle()</script>', false)
            ->assertSee('&lt;script&gt;untrustedTitle()&lt;/script&gt;', false);
    }

    public function test_unknown_and_inactive_modules_cannot_be_opened_or_linked(): void
    {
        $this->get('/courses/not-a-course')->assertNotFound();
        Course::where('code', 'AI-DM')->update(['is_active' => false]);
        $this->get('/courses/ai-dm')->assertNotFound();
        $this->get('/')->assertOk()->assertDontSee(route('public.courses.show', 'ai-dm'), false);
    }
}
