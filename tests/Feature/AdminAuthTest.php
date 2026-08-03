<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_admin_can_log_in_with_correct_credentials(): void
    {
        $admin = Admin::factory()->create([
            'password' => Hash::make('password123'),
            'status'   => 1,
            'login'    => 1,
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email'    => $admin->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $admin = Admin::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email'    => $admin->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_inactive_admin_cannot_log_in(): void
    {
        $admin = Admin::factory()->create([
            'password' => Hash::make('password123'),
            'status'   => 0, // deactivated
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email'    => $admin->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_admin_with_login_blocked_cannot_log_in(): void
    {
        $admin = Admin::factory()->create([
            'password' => Hash::make('password123'),
            'status'   => 1,
            'login'    => 0, // login explicitly disabled
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'email'    => $admin->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_login_is_rate_limited_after_repeated_failures(): void
    {
        $admin = Admin::factory()->create(['password' => Hash::make('password123')]);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admin.login.submit'), [
                'email'    => $admin->email,
                'password' => 'wrong-password',
            ]);
        }

        // The 6th attempt should be blocked by the RateLimiter in
        // LoginController, even with the correct password this time.
        $response = $this->post(route('admin.login.submit'), [
            'email'    => $admin->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_guest_is_redirected_away_from_protected_routes(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_admin_is_redirected_away_from_login_page(): void
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.login'));

        $response->assertRedirect(route('admin.dashboard'));
    }
}
