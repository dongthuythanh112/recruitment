<?php

namespace Tests\Feature\Client;

use App\Models\Company;
use App\Models\Industry;
use App\Models\Job;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class JobDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function job(array $attributes = [], array $companyAttributes = []): Job
    {
        return Job::create([
            'company_id' => Company::create(['name' => 'Công ty '.fake()->unique()->word(), ...$companyAttributes])->id,
            'created_by' => User::factory()->recruiter()->create()->id,
            'industry_id' => Industry::create(['name' => 'Ngành '.fake()->unique()->word()])->id,
            'title' => 'Lập trình viên PHP',
            'description' => 'Mô tả',
            'status' => Job::STATUS_APPROVED,
            'published_at' => now(),
            ...$attributes,
        ]);
    }

    public function test_guest_sees_full_job_details(): void
    {
        $job = $this->job([
            'title' => 'Kế toán trưởng',
            'description' => 'Lập báo cáo tài chính',
            'requirements' => 'Có chứng chỉ hành nghề',
            'benefits' => 'Lương tháng 13',
            'location' => 'Đà Nẵng',
            'salary_min' => 20000000,
            'salary_max' => 30000000,
            'employment_type' => 'full_time',
            'workplace_type' => 'remote',
            'experience_years' => 3,
            'quantity' => 2,
            'deadline' => today()->addDays(5),
        ], ['name' => 'Sao Mai Group', 'address' => 'Hải Châu, Đà Nẵng', 'company_size' => '50-100']);
        $job->skills()->attach(Skill::create(['name' => 'Excel'])->id, ['is_required' => true, 'created_at' => now()]);

        $this->get("/viec-lam/{$job->slug}")->assertOk()
            ->assertSee('Kế toán trưởng')->assertSee('Sao Mai Group')
            ->assertSee('Lập báo cáo tài chính')->assertSee('Có chứng chỉ hành nghề')->assertSee('Lương tháng 13')
            ->assertSee('Đà Nẵng')->assertSee('20 - 30 triệu')->assertSee('Toàn thời gian')->assertSee('Từ xa')
            ->assertSee('Từ 3 năm')->assertSee('2 người')->assertSee('Excel')->assertSee('Còn 5 ngày')
            ->assertSee('Hải Châu, Đà Nẵng');
    }

    #[DataProvider('hiddenJobs')]
    public function test_jobs_not_on_the_board_return_404(array $attributes): void
    {
        $job = $this->job($attributes);

        $this->get("/viec-lam/{$job->slug}")->assertNotFound();
    }

    public static function hiddenJobs(): array
    {
        return [
            'pending' => [['status' => Job::STATUS_PENDING]],
            'rejected' => [['status' => Job::STATUS_REJECTED]],
            'hidden' => [['status' => Job::STATUS_HIDDEN]],
            'draft' => [['status' => Job::STATUS_DRAFT]],
            'closed' => [['status' => Job::STATUS_CLOSED]],
            'expired' => [['deadline' => '2020-01-01']],
        ];
    }

    public function test_unknown_slug_returns_404(): void
    {
        $this->get('/viec-lam/khong-co-tin-nay')->assertNotFound();
    }

    public function test_deadline_today_is_still_open(): void
    {
        $job = $this->job(['deadline' => today()]);

        $this->get("/viec-lam/{$job->slug}")->assertOk()->assertSee('Hết hạn hôm nay');
    }

    public function test_home_rows_link_to_the_detail_page(): void
    {
        $job = $this->job();

        $this->get('/')->assertSee(route('jobs.show', $job->slug));
    }

    public function test_related_jobs_are_open_jobs_of_the_same_industry(): void
    {
        $industry = Industry::create(['name' => 'Công nghệ']);
        $job = $this->job(['title' => 'Tin Chính', 'industry_id' => $industry->id]);
        $this->job(['title' => 'Cùng Ngành', 'industry_id' => $industry->id]);
        $this->job(['title' => 'Cùng Ngành Chờ Duyệt', 'industry_id' => $industry->id, 'status' => Job::STATUS_PENDING]);
        $this->job(['title' => 'Khác Ngành']);

        $related = $this->get("/viec-lam/{$job->slug}")->assertOk()->viewData('relatedJobs');

        $this->assertSame(['Cùng Ngành'], $related->pluck('title')->all());
    }

    public function test_apply_button_only_appears_when_company_has_email(): void
    {
        $withEmail = $this->job(['title' => 'Có Email'], ['email' => 'hr@congty.example']);
        $withoutEmail = $this->job(['title' => 'Không Email']);

        $this->get("/viec-lam/{$withEmail->slug}")->assertSee('mailto:hr@congty.example', false)->assertSee('Gửi hồ sơ qua email');
        $this->get("/viec-lam/{$withoutEmail->slug}")->assertDontSee('Gửi hồ sơ qua email')->assertSee('Công ty chưa để lại email');
    }

    public function test_company_website_is_only_linked_for_http_urls(): void
    {
        $safe = $this->job(['title' => 'An Toàn'], ['website' => 'https://congty.example']);
        $unsafe = $this->job(['title' => 'Không An Toàn'], ['website' => 'javascript:alert(1)']);

        $this->get("/viec-lam/{$safe->slug}")->assertSee('href="https://congty.example"', false);
        $this->get("/viec-lam/{$unsafe->slug}")->assertDontSee('javascript:alert(1)', false);
    }
}
