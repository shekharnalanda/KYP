<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_password_page(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('profile.password.edit'))
            ->assertOk()
            ->assertSee('Change Password');
    }

    public function test_user_can_change_password_with_current_password(): void
    {
        $user = User::factory()->create([
            'password' => 'OldPassword@123',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->put(route('profile.password.update'), [
            'current_password' => 'OldPassword@123',
            'password' => 'NewPassword@456',
            'password_confirmation' => 'NewPassword@456',
        ]);

        $response->assertRedirect(route('profile.password.edit'));
        $response->assertSessionHas('status');
        $this->assertTrue(Hash::check('NewPassword@456', $user->fresh()->password));
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $user = User::factory()->create([
            'password' => 'OldPassword@123',
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($user)->put(route('profile.password.update'), [
            'current_password' => 'WrongPassword@123',
            'password' => 'NewPassword@456',
            'password_confirmation' => 'NewPassword@456',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('OldPassword@123', $user->fresh()->password));
    }
}
