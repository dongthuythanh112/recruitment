<?php

namespace Tests\Feature\Client;

use App\Models\Company;
use App\Models\Industry;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function job(array $attributes = []): Job
    {
        return Job::create([
            'company_id' => Company::create(['name' => 'Công ty '.fake()->unique()->word()])->id,
            'created_by' => User::factory()->recruiter()->create()->id,
            'industry_id' => Industry::create(['name' => 'Ngành '.fake()->unique()->word()])->id,
            'title' => 'Lập trình viên PHP',
            'description' => 'Mô tả',
            'status' => Job::STATUS_APPROVED,
            'published_at' => now(),
            ...$attributes,
        ]);
    }

    public function test_guests_only_see_jobs_that_are_open(): void
    {
        $this->job(['title' => 'Không Hạn']);
        $this->job(['title' => 'Hạn Hôm Nay', 'deadline' => today()]);
        $this->job(['title' => 'Quá Hạn', 'deadline' => today()->subDay()]);
        $this->job(['title' => 'Tin Chờ', 'status' => Job::STATUS_PENDING]);
        $this->job(['title' => 'Tin Ẩn', 'status' => Job::STATUS_HIDDEN]);
        $this->job(['title' => 'Tin Từ Chối', 'status' => Job::STATUS_REJECTED]);

        $response = $this->get('/')->assertOk()
            ->assertSee('Không Hạn')->assertSee('Hạn Hôm Nay')->assertSee('Hết hạn hôm nay')
            ->assertDontSee('Quá Hạn')->assertDontSee('Tin Chờ')->assertDontSee('Tin Ẩn')->assertDontSee('Tin Từ Chối');

        $this->assertSame(2, $response->viewData('openJobsCount'));
        $this->assertSame(2, $response->viewData('hiringCompaniesCount'));
    }

    public function test_search_matches_title_company_and_location(): void
    {
        $company = Company::create(['name' => 'Sao Mai Group']);
        $this->job(['title' => 'Kế Toán', 'location' => 'Đà Nẵng']);
        $this->job(['title' => 'Lái Xe', 'company_id' => $company->id, 'location' => 'Hà Nội']);

        $this->get('/?q=toán')->assertSee('Kế Toán')->assertDontSee('Lái Xe');
        $this->get('/?q=Sao+Mai')->assertSee('Lái Xe')->assertDontSee('Kế Toán');
        $this->get('/?location=Đà')->assertSee('Kế Toán')->assertDontSee('Lái Xe');
        $this->get('/?q=toán&location=Hà')->assertDontSee('Kế Toán')->assertSee('Không có việc làm nào khớp');
    }

    public function test_keyword_search_cannot_reach_unpublished_jobs_through_company_name(): void
    {
        $company = Company::create(['name' => 'Sao Mai Group']);
        $this->job(['title' => 'Tin Chờ', 'company_id' => $company->id, 'status' => Job::STATUS_PENDING]);

        $this->get('/?q=Sao+Mai')->assertDontSee('Tin Chờ');
    }

    public function test_parent_industry_includes_jobs_of_its_children(): void
    {
        $parent = Industry::create(['name' => 'Công nghệ']);
        $child = Industry::create(['name' => 'Phần mềm', 'parent_id' => $parent->id]);
        $this->job(['title' => 'Việc Con', 'industry_id' => $child->id]);
        $this->job(['title' => 'Việc Khác']);

        $this->get("/?industry_id={$parent->id}")->assertSee('Việc Con')->assertDontSee('Việc Khác');

        $top = $this->get('/')->viewData('industries')->firstWhere('id', $parent->id);
        $this->assertSame(1, $top->open_jobs_count);
        $this->assertNull($this->get('/')->viewData('industries')->firstWhere('id', $child->id));
    }

    public function test_companies_count_only_open_jobs(): void
    {
        $company = Company::create(['name' => 'Sao Mai Group']);
        $this->job(['company_id' => $company->id]);
        $this->job(['company_id' => $company->id, 'status' => Job::STATUS_PENDING]);
        Company::create(['name' => 'Chưa Tuyển']);

        $companies = $this->get('/')->viewData('companies');

        $this->assertSame(1, $companies->firstWhere('id', $company->id)->open_jobs_count);
        $this->assertNull($companies->firstWhere('name', 'Chưa Tuyển'));
    }

    public function test_malformed_query_is_refused_without_an_error_page(): void
    {
        $this->get('/?q[]=x')->assertRedirect();
        $this->get('/?industry_id=abc')->assertRedirect();
        $this->get('/?industry_id=999999')->assertOk();
    }

    public function test_board_row_shows_short_salary_and_full_title(): void
    {
        $this->job([
            'title' => 'Trưởng phòng kinh doanh khu vực miền Bắc',
            'salary_min' => 12500000,
            'salary_max' => 20000000,
        ]);

        $this->get('/')
            ->assertSee('12,5 - 20 triệu')
            ->assertSee('Trưởng phòng kinh doanh khu vực miền Bắc');
    }

    public function test_salary_short_covers_every_shape(): void
    {
        $this->assertSame('Từ 10 triệu', (new Job(['salary_min' => 10000000]))->salaryShort());
        $this->assertSame('Đến 8,5 triệu', (new Job(['salary_max' => 8500000]))->salaryShort());
        $this->assertSame('Thỏa thuận', (new Job)->salaryShort());
        $this->assertSame('1.000 - 2.000 USD', (new Job(['salary_min' => 1000, 'salary_max' => 2000, 'salary_currency' => 'USD']))->salaryShort());
    }

    public function test_employer_section_links_to_the_configured_contact(): void
    {
        config(['app.contact_email' => 'tuyendung@congty.example']);

        $this->get('/')->assertSee('mailto:tuyendung@congty.example', false)->assertSee('Gửi yêu cầu đăng tin');
    }

    public function test_home_renders_without_any_data(): void
    {
        $this->get('/')->assertOk()->assertSee('Hiện chưa có việc làm nào đang tuyển.');
    }
}
