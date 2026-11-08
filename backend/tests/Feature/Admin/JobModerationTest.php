<?php

namespace Tests\Feature\Admin;

use App\Models\Job;
use PHPUnit\Framework\Attributes\DataProvider;

class JobModerationTest extends AdminTestCase
{
    public function test_pending_tab_is_a_queue_with_oldest_job_first(): void
    {
        $this->job(['title' => 'Đã Xong', 'status' => Job::STATUS_APPROVED]);
        $this->job(['title' => 'Gửi Trước'])->forceFill(['created_at' => now()->subDay()])->save();
        $this->job(['title' => 'Gửi Sau']);

        $this->get('/admin/jobs?status=pending')
            ->assertOk()
            ->assertSeeInOrder(['Gửi Trước', 'Gửi Sau'])
            ->assertDontSee('Đã Xong');
    }

    public function test_approve_button_only_appears_on_pending_jobs(): void
    {
        $pending = $this->job();
        $approved = $this->job(['status' => Job::STATUS_APPROVED]);

        $this->get('/admin/jobs')
            ->assertSee(route('admin.jobs.moderate', $pending))
            ->assertDontSee(route('admin.jobs.moderate', $approved));
    }

    public function test_approving_from_a_tab_returns_to_that_tab(): void
    {
        $job = $this->job();

        $this->from('/admin/jobs?status=pending')
            ->post("/admin/jobs/{$job->id}/moderate", ['action' => 'approve'])
            ->assertRedirect('/admin/jobs?status=pending');
    }

    public function test_separate_moderation_screen_is_gone(): void
    {
        $this->get('/admin/job-moderation')->assertNotFound();
        $this->get('/admin/statistics')->assertOk()->assertDontSee('Duyệt tin');
    }

    public function test_approve_publishes_and_records_history(): void
    {
        $job = $this->job();

        $this->post("/admin/jobs/{$job->id}/moderate", ['action' => 'approve'])->assertSessionHas('success');

        $job->refresh();
        $this->assertSame(Job::STATUS_APPROVED, $job->status);
        $this->assertSame($this->admin->id, $job->reviewed_by);
        $this->assertNotNull($job->reviewed_at);
        $this->assertNotNull($job->published_at);
        $this->assertDatabaseHas('job_moderation_histories', ['job_id' => $job->id, 'admin_id' => $this->admin->id, 'action' => 'approve']);
    }

    public function test_reject_requires_reason(): void
    {
        $job = $this->job();

        $this->post("/admin/jobs/{$job->id}/moderate", ['action' => 'reject'])->assertSessionHasErrors('note');
        $this->assertSame(Job::STATUS_PENDING, $job->fresh()->status);

        $this->post("/admin/jobs/{$job->id}/moderate", ['action' => 'reject', 'note' => 'Thiếu thông tin']);

        $job->refresh();
        $this->assertSame(Job::STATUS_REJECTED, $job->status);
        $this->assertSame('Thiếu thông tin', $job->rejection_reason);
        $this->assertNull($job->published_at);
        $this->assertDatabaseHas('job_moderation_histories', ['job_id' => $job->id, 'action' => 'reject', 'note' => 'Thiếu thông tin']);
    }

    public function test_hide_and_restore_keep_original_publish_date(): void
    {
        $job = $this->job();
        $this->post("/admin/jobs/{$job->id}/moderate", ['action' => 'approve']);
        $publishedAt = $job->fresh()->published_at;

        $this->travel(1)->day();

        $this->post("/admin/jobs/{$job->id}/moderate", ['action' => 'hide']);
        $this->assertSame(Job::STATUS_HIDDEN, $job->fresh()->status);

        $this->post("/admin/jobs/{$job->id}/moderate", ['action' => 'restore']);
        $job->refresh();
        $this->assertSame(Job::STATUS_APPROVED, $job->status);
        $this->assertTrue($publishedAt->equalTo($job->published_at));
        $this->assertSame(3, $job->moderationHistories()->count());
    }

    #[DataProvider('invalidTransitions')]
    public function test_invalid_transitions_are_refused_and_not_recorded(string $status, string $action): void
    {
        $job = $this->job(['status' => $status]);

        $this->post("/admin/jobs/{$job->id}/moderate", ['action' => $action, 'note' => 'x'])->assertSessionHasErrors('action');

        $this->assertSame($status, $job->fresh()->status);
        $this->assertSame(0, $job->moderationHistories()->count());
    }

    public static function invalidTransitions(): array
    {
        return [
            'approve an approved job' => [Job::STATUS_APPROVED, 'approve'],
            'approve a rejected job' => [Job::STATUS_REJECTED, 'approve'],
            'approve a draft' => [Job::STATUS_DRAFT, 'approve'],
            'reject a hidden job' => [Job::STATUS_HIDDEN, 'reject'],
            'hide a pending job' => [Job::STATUS_PENDING, 'hide'],
            'restore an approved job' => [Job::STATUS_APPROVED, 'restore'],
            'restore a closed job' => [Job::STATUS_CLOSED, 'restore'],
        ];
    }

    public function test_unknown_action_is_refused(): void
    {
        $job = $this->job();

        $this->post("/admin/jobs/{$job->id}/moderate", ['action' => 'delete'])->assertSessionHasErrors('action');
    }

    public function test_job_page_shows_actions_for_each_status(): void
    {
        $this->get('/admin/jobs/'.$this->job()->id)->assertSee('Duyệt tin')->assertSee('Từ chối');
        $this->get('/admin/jobs/'.$this->job(['status' => Job::STATUS_APPROVED])->id)->assertSee('Ẩn tin');
        $this->get('/admin/jobs/'.$this->job(['status' => Job::STATUS_HIDDEN])->id)->assertSee('Khôi phục tin');
    }
}
