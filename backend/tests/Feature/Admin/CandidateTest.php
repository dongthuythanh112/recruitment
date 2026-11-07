<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CandidateTest extends AdminTestCase
{
    private function payload(array $overrides = []): array
    {
        return [
            'name' => 'Nguyễn Văn A',
            'email' => 'a@example.test',
            'phone' => '0900000000',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 'active',
            'desired_position' => 'Kế toán',
            ...$overrides,
        ];
    }

    public function test_index_lists_and_filters(): void
    {
        $match = $this->candidate();
        $other = $this->candidate();
        $match->user->update(['name' => 'Tìm Thấy']);
        $other->user->update(['name' => 'Người Khác']);

        $this->get('/admin/candidates')->assertOk()->assertSee('Tìm Thấy')->assertSee('Người Khác');
        $this->get('/admin/candidates?q=Thấy')->assertOk()->assertSee('Tìm Thấy')->assertDontSee('Người Khác');
    }

    public function test_store_creates_user_with_candidate_role_and_profile(): void
    {
        $this->post('/admin/candidates', $this->payload(['role' => 'admin']))->assertRedirect();

        $user = User::where('email', 'a@example.test')->firstOrFail();
        $this->assertSame(User::ROLE_CANDIDATE, $user->role, 'role must not be settable from the form');
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertSame('Kế toán', $user->candidateProfile->desired_position);
    }

    public function test_store_validates(): void
    {
        $existing = User::factory()->create();

        $this->post('/admin/candidates', $this->payload(['email' => $existing->email, 'password_confirmation' => 'nope']))
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_salary_max_alone_is_allowed_but_must_not_be_below_min(): void
    {
        $this->post('/admin/candidates', $this->payload(['desired_salary_max' => 1000]))->assertSessionHasNoErrors();
        $this->post('/admin/candidates', $this->payload(['email' => 'b@example.test', 'desired_salary_min' => 2000, 'desired_salary_max' => 1000]))
            ->assertSessionHasErrors('desired_salary_max');
    }

    public function test_update_keeps_password_when_left_blank(): void
    {
        $candidate = $this->candidate();
        $hash = $candidate->user->password;

        $this->put("/admin/candidates/{$candidate->id}", $this->payload([
            'email' => $candidate->user->email,
            'password' => '',
            'password_confirmation' => '',
            'name' => 'Tên Mới',
        ]))->assertRedirect();

        $user = $candidate->user->fresh();
        $this->assertSame('Tên Mới', $user->name);
        $this->assertSame($hash, $user->password);
    }

    public function test_show_and_edit_render(): void
    {
        $candidate = $this->candidate();
        $this->application($this->job(), $candidate);

        $this->get("/admin/candidates/{$candidate->id}")->assertOk()->assertSee($candidate->user->name)->assertSee('Lập trình viên PHP');
        $this->get("/admin/candidates/{$candidate->id}/edit")->assertOk();
        $this->get('/admin/candidates/create')->assertOk();
    }

    public function test_toggle_status_locks_and_unlocks(): void
    {
        $candidate = $this->candidate();

        $this->patch("/admin/candidates/{$candidate->id}/toggle-status");
        $this->assertSame(User::STATUS_LOCKED, $candidate->user->fresh()->status);

        $this->patch("/admin/candidates/{$candidate->id}/toggle-status");
        $this->assertSame(User::STATUS_ACTIVE, $candidate->user->fresh()->status);
    }

    public function test_destroy_removes_account_and_profile(): void
    {
        $candidate = $this->candidate();
        $userId = $candidate->user_id;

        $this->delete("/admin/candidates/{$candidate->id}")->assertRedirect('/admin/candidates');

        $this->assertDatabaseMissing('users', ['id' => $userId]);
        $this->assertDatabaseMissing('candidate_profiles', ['id' => $candidate->id]);
    }

    public function test_destroy_is_blocked_when_candidate_has_applications(): void
    {
        $candidate = $this->candidate();
        $this->application($this->job(), $candidate);

        $this->delete("/admin/candidates/{$candidate->id}")->assertSessionHas('error');

        $this->assertDatabaseHas('candidate_profiles', ['id' => $candidate->id]);
    }

    public function test_missing_candidate_returns_404(): void
    {
        $this->get('/admin/candidates/999999')->assertNotFound();
    }
}
