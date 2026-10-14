<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\LearningSession;
use App\Models\LearningSessionProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FreeSessionNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_open_and_save_six_sessions_in_any_order_without_completing_any(): void
    {
        $student = User::factory()->create(['role'=>'student', 'status'=>'active']);
        $course = Course::create(['code'=>'CIT', 'name'=>'Computer skills', 'total_sessions'=>6, 'total_hours'=>12, 'position'=>1, 'is_active'=>true]);
        $sessions = collect(range(1,6))->map(fn ($number) => LearningSession::create([
            'course_id'=>$course->id, 'session_number'=>$number, 'title_hi'=>"Session {$number}",
            'duration_minutes'=>120, 'content_status'=>'published', 'required_active_minutes'=>120,
            'passing_score'=>60,
        ]));
        foreach ([5,2,0,4,1,3] as $index) {
            $session = $sessions[$index];
            $this->actingAs($student)->get(route('learning.show', $session))
                ->assertOk()->assertDontSee('aria-disabled="true"', false)
                ->assertSee(route('learning.show', $sessions[5]), false);
            $this->postJson(route('learning.progress', $session), [
                'current_step'=>2, 'active_seconds'=>0, 'completed_steps'=>[],
            ])->assertOk()->assertJson(['saved'=>true]);
        }
        $this->assertSame(6, LearningSessionProgress::where('user_id',$student->id)->count());
        $this->assertSame(0, LearningSessionProgress::where('user_id',$student->id)->whereNotNull('completed_at')->count());
        $this->post(route('learning.complete', $sessions[5]))->assertSessionHasErrors('session');
    }
}
