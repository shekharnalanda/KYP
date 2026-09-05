<?php

namespace Tests\Feature;

use App\Models\IrisProfile;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LearningSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class IrisConnectorApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:'.base64_encode(str_repeat('k', 32)),
            'iris.connector_token' => 'test-iris-token',
        ]);
    }

    public function test_health_requires_connector_token(): void
    {
        $this->getJson('/api/iris/health')->assertUnauthorized();

        $this->withToken('test-iris-token')
            ->getJson('/api/iris/health')
            ->assertOk()
            ->assertJsonPath('device_model', 'Mantra MIS100V2');
    }

    public function test_connector_can_store_encrypted_iris_enrollment(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
            'student_id' => 'KYP-IRIS-001',
        ]);

        $this->withToken('test-iris-token')->postJson('/api/iris/enroll', [
            'user_id' => $student->id,
            'left_template' => 'left-template-payload',
            'right_template' => 'right-template-payload',
            'quality_score' => 91,
            'device_reference' => 'MIS100V2-LAB-01',
        ])->assertOk()->assertJsonPath('ok', true);

        $profile = IrisProfile::where('user_id', $student->id)->firstOrFail();
        $this->assertSame('left-template-payload', $profile->left_template);
        $this->assertSame('right-template-payload', $profile->right_template);
        $this->assertNotSame(
            'left-template-payload',
            $profile->getRawOriginal('left_template')
        );
    }

    public function test_connector_can_fetch_catalog_and_active_students(): void
    {
        $course = Course::create([
            'code' => 'CIT',
            'name' => 'Information Technology',
            'total_sessions' => 60,
            'total_hours' => 120,
            'minimum_exam_sessions' => 48,
            'position' => 1,
            'is_active' => true,
        ]);
        $session = LearningSession::create([
            'course_id' => $course->id,
            'session_number' => 1,
            'title_hi' => 'कंप्यूटर परिचय',
            'title_en' => 'Computer Introduction',
            'duration_minutes' => 120,
            'content_status' => 'published',
            'published_at' => now(),
        ]);
        $student = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
            'student_id' => 'KYP-IRIS-002',
        ]);
        Enrollment::create([
            'user_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
        ]);

        $this->withToken('test-iris-token')
            ->getJson('/api/iris/catalog')
            ->assertOk()
            ->assertJsonPath('data.0.id', $course->id)
            ->assertJsonPath('data.0.sessions.0.id', $session->id);

        $this->withToken('test-iris-token')
            ->getJson('/api/iris/students')
            ->assertOk()
            ->assertJsonPath('data.0.id', $student->id)
            ->assertJsonPath('data.0.iris_enrolled', false);
    }
}
