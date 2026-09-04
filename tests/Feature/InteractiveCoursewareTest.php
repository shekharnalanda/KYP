<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\LearningSession;
use App\Models\LearningSessionProgress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InteractiveCoursewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_progress_is_saved_but_unfinished_session_is_not_completed(): void
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $course = Course::create(['code'=>'TST','name'=>'Test','total_sessions'=>1,'total_hours'=>2,'position'=>1,'is_active'=>true]);
        $session = LearningSession::create(['course_id'=>$course->id,'session_number'=>1,'title_hi'=>'परीक्षण','duration_minutes'=>120,'content_status'=>'published','required_active_minutes'=>120,'passing_score'=>60]);

        $this->actingAs($student)->get(route('learning.show', $session))->assertOk();

        $this->actingAs($student)->postJson(route('learning.progress', $session), [
            'current_step' => 1,
            'active_seconds' => 15,
            'completed_steps' => ['orientation'],
            'quiz_answers' => [],
        ])->assertOk()->assertJson(['saved' => true]);

        $this->assertDatabaseHas('learning_session_progress', [
            'user_id' => $student->id,
            'learning_session_id' => $session->id,
            'active_seconds' => 15,
        ]);

        $this->actingAs($student)->post(route('learning.complete', $session))
            ->assertSessionHasErrors('session');
    }
}
