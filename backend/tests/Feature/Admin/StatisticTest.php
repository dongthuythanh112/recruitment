<?php

namespace Tests\Feature\Admin;

use App\Models\Job;

class StatisticTest extends AdminTestCase
{
    public function test_renders_with_empty_database(): void
    {
        $this->get('/admin/statistics')->assertOk()->assertSee('Thống kê hệ thống')->assertSee('Chưa có dữ liệu');
    }

    public function test_counts_are_correct(): void
    {
        $this->candidate();
        $this->candidate();
        $this->recruiter();
        $job = $this->job();
        $this->job(['status' => Job::STATUS_APPROVED]);
        $this->application($job, $this->candidate());

        $response = $this->get('/admin/statistics')->assertOk();

        $cards = collect($response->viewData('cards'))->pluck('value', 'label');
        $this->assertSame(3, $cards['Ứng viên']);
        $this->assertSame(3, $cards['Nhà tuyển dụng']); // 1 + 2 người đăng do $this->job() tạo
        $this->assertSame(2, $cards['Tin tuyển dụng']);
        $this->assertSame(1, $cards['Tin chờ duyệt']);
        $this->assertSame(1, $cards['Lượt ứng tuyển']);

        $byStatus = $response->viewData('jobsByStatus')->pluck('value', 'label');
        $this->assertSame(1, $byStatus['Chờ duyệt']);
        $this->assertSame(1, $byStatus['Đã duyệt']);
        $this->assertSame(0, $byStatus['Nháp']);
    }

    public function test_monthly_series_covers_six_months_and_ignores_older_rows(): void
    {
        $this->travelTo(now()->setDate(2026, 3, 31)); // cuối tháng: phép trừ tháng không được nhảy sai
        $this->job();
        $this->travelTo(now()->setDate(2025, 1, 15));
        $this->job(['title' => 'Quá cũ']);
        $this->travelTo(now()->setDate(2026, 3, 31));

        $rows = $this->get('/admin/statistics')->assertOk()->viewData('newJobsByMonth');

        $this->assertSame(['10/25', '11/25', '12/25', '01/26', '02/26', '03/26'], $rows->pluck('label')->all());
        $this->assertSame([0, 0, 0, 0, 0, 1], $rows->pluck('value')->all());
    }
}
