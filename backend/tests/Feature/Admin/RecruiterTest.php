<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\User;

class RecruiterTest extends AdminTestCase
{
    private function payload(array $overrides = []): array
    {
        return [
            'name' => 'Trần B',
            'email' => 'b@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'status' => 'active',
            'position' => 'HR',
            ...$overrides,
        ];
    }

    public function test_store_with_existing_company(): void
    {
        $company = Company::create(['name' => 'Công ty Cũ']);

        $this->post('/admin/recruiters', $this->payload(['company_id' => $company->id]))->assertRedirect();

        $user = User::where('email', 'b@example.test')->firstOrFail();
        $this->assertSame(User::ROLE_RECRUITER, $user->role);
        $this->assertSame($company->id, $user->recruiterProfile->company_id);
    }

    public function test_store_with_new_company_creates_it_with_vietnamese_slug(): void
    {
        $this->post('/admin/recruiters', $this->payload(['new_company_name' => 'Công ty Đại Việt']))->assertRedirect();

        $company = Company::where('name', 'Công ty Đại Việt')->firstOrFail();
        $this->assertSame('cong-ty-dai-viet', $company->slug);
        $this->assertSame($this->admin->id, $company->created_by);
        $this->assertSame($company->id, User::where('email', 'b@example.test')->first()->recruiterProfile->company_id);
    }

    public function test_show_includes_the_recruiters_jobs_only(): void
    {
        $recruiter = $this->recruiter();
        $this->job(['created_by' => $recruiter->user_id, 'title' => 'Tin Của Tôi']);
        $this->job(['title' => 'Tin Người Khác']);

        $this->get("/admin/recruiters/{$recruiter->id}")->assertOk()->assertSee('Tin Của Tôi')->assertDontSee('Tin Người Khác');
    }

    public function test_index_edit_create_render(): void
    {
        $recruiter = $this->recruiter();

        $this->get('/admin/recruiters')->assertOk()->assertSee($recruiter->user->name);
        $this->get("/admin/recruiters/{$recruiter->id}/edit")->assertOk();
        $this->get('/admin/recruiters/create')->assertOk();
    }

    public function test_update_and_toggle_status(): void
    {
        $recruiter = $this->recruiter();

        $this->put("/admin/recruiters/{$recruiter->id}", $this->payload([
            'email' => $recruiter->user->email,
            'password' => '',
            'password_confirmation' => '',
            'position' => 'Trưởng phòng',
        ]))->assertRedirect();
        $this->assertSame('Trưởng phòng', $recruiter->fresh()->position);

        $this->patch("/admin/recruiters/{$recruiter->id}/toggle-status");
        $this->assertSame(User::STATUS_LOCKED, $recruiter->user->fresh()->status);
    }

    public function test_destroy_is_blocked_when_recruiter_has_jobs(): void
    {
        $recruiter = $this->recruiter();
        $job = $this->job(['created_by' => $recruiter->user_id]);

        $this->delete("/admin/recruiters/{$recruiter->id}")->assertSessionHas('error');
        $this->assertDatabaseHas('recruiter_profiles', ['id' => $recruiter->id]);

        $job->delete();
        $this->delete("/admin/recruiters/{$recruiter->id}")->assertRedirect('/admin/recruiters');
        $this->assertDatabaseMissing('users', ['id' => $recruiter->user_id]);
    }
}
