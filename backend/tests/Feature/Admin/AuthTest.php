<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/statistics')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertOk()->assertSee('Đăng nhập quản trị');
    }

    public function test_admin_can_log_in_and_out(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'secret-pass']);

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'secret-pass'])
            ->assertRedirect('/admin/statistics');
        $this->assertAuthenticatedAs($admin);

        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'secret-pass']);

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_non_admin_and_locked_admin_cannot_log_in(): void
    {
        $candidate = User::factory()->create(['password' => 'secret-pass']);
        $locked = User::factory()->admin()->locked()->create(['password' => 'secret-pass']);

        foreach ([$candidate, $locked] as $user) {
            $this->post('/admin/login', ['email' => $user->email, 'password' => 'secret-pass'])->assertSessionHasErrors('email');
            $this->assertGuest();
        }
    }

    public function test_non_admin_session_cannot_open_admin_pages(): void
    {
        $this->actingAs(User::factory()->recruiter()->create())
            ->get('/admin/statistics')
            ->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_admin_locked_mid_session_is_logged_out(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get('/admin/statistics')->assertOk();

        $admin->update(['status' => User::STATUS_LOCKED]);

        $this->get('/admin/statistics')->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_login_is_throttled_after_repeated_failures(): void
    {
        $admin = User::factory()->admin()->create();

        foreach (range(1, 5) as $attempt) {
            $this->post('/admin/login', ['email' => $admin->email, 'password' => 'wrong']);
        }

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'wrong'])->assertStatus(429);
    }
}
