<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\Industry;
use App\Models\Job;
use App\Models\Skill;

class JobTest extends AdminTestCase
{
    private function payload(array $overrides = []): array
    {
        return [
            'company_id' => Company::create(['name' => 'Công ty '.fake()->unique()->word()])->id,
            'industry_id' => Industry::create(['name' => 'Ngành '.fake()->unique()->word()])->id,
            'title' => 'Kế toán trưởng',
            'description' => 'Mô tả công việc',
            'quantity' => 2,
            ...$overrides,
        ];
    }

    public function test_store_creates_pending_job_with_slug_and_skills(): void
    {
        $skill = Skill::create(['name' => 'Excel']);

        $this->post('/admin/jobs', $this->payload(['skills' => [$skill->id], 'status' => 'approved']))->assertRedirect();

        $job = Job::where('title', 'Kế toán trưởng')->firstOrFail();
        $this->assertSame(Job::STATUS_PENDING, $job->status, 'status must not be settable from the form');
        $this->assertSame('ke-toan-truong', $job->slug);
        $this->assertSame($this->admin->id, $job->created_by);
        $this->assertSame([$skill->id], $job->skills->pluck('id')->all());
        $this->assertNotNull($job->skills->first()->pivot->created_at);
    }

    public function test_duplicate_titles_get_unique_slugs(): void
    {
        $first = $this->job(['title' => 'Trùng Tên']);
        $second = $this->job(['title' => 'Trùng Tên']);

        $this->assertSame('trung-ten', $first->slug);
        $this->assertSame('trung-ten-2', $second->slug);
    }

    public function test_store_validates_salary_range(): void
    {
        $this->post('/admin/jobs', $this->payload(['salary_min' => 200, 'salary_max' => 100]))->assertSessionHasErrors('salary_max');
        $this->post('/admin/jobs', $this->payload(['title' => '']))->assertSessionHasErrors('title');
        $this->post('/admin/jobs', $this->payload(['company_id' => 999999]))->assertSessionHasErrors('company_id');
    }

    public function test_index_filters_by_status_and_creator(): void
    {
        $recruiter = $this->recruiter();
        $this->job(['title' => 'Tin Một', 'created_by' => $recruiter->user_id]);
        $this->job(['title' => 'Tin Hai', 'status' => Job::STATUS_APPROVED]);

        $this->get('/admin/jobs')->assertOk()->assertSee('Tin Một')->assertSee('Tin Hai');
        $this->get('/admin/jobs?status=approved')->assertOk()->assertDontSee('Tin Một')->assertSee('Tin Hai');
        $this->get("/admin/jobs?created_by={$recruiter->user_id}")->assertOk()->assertSee('Tin Một')->assertDontSee('Tin Hai');
    }

    public function test_show_edit_create_render(): void
    {
        $job = $this->job();

        $this->get("/admin/jobs/{$job->id}")->assertOk()->assertSee($job->title);
        $this->get("/admin/jobs/{$job->id}/edit")->assertOk();
        $this->get('/admin/jobs/create')->assertOk();
    }

    public function test_update_changes_content_but_not_status_or_slug(): void
    {
        $job = $this->job(['status' => Job::STATUS_APPROVED]);
        $slug = $job->slug;
        $skill = Skill::create(['name' => 'Excel']);

        $this->put("/admin/jobs/{$job->id}", $this->payload(['title' => 'Tiêu Đề Mới', 'skills' => [$skill->id]]))->assertRedirect();
        $this->put("/admin/jobs/{$job->id}", $this->payload(['title' => 'Tiêu Đề Mới']))->assertRedirect();

        $job->refresh();
        $this->assertSame('Tiêu Đề Mới', $job->title);
        $this->assertSame(Job::STATUS_APPROVED, $job->status);
        $this->assertSame($slug, $job->slug);
        $this->assertCount(0, $job->skills, 'unticking all skills must clear them');
    }

    public function test_destroy_is_blocked_when_job_has_applications(): void
    {
        $job = $this->job();
        $this->application($job, $this->candidate());

        $this->delete("/admin/jobs/{$job->id}")->assertSessionHas('error');
        $this->assertDatabaseHas('jobs', ['id' => $job->id]);
    }

    public function test_destroy_removes_job(): void
    {
        $job = $this->job();

        $this->delete("/admin/jobs/{$job->id}")->assertRedirect('/admin/jobs');
        $this->assertDatabaseMissing('jobs', ['id' => $job->id]);
    }
}
