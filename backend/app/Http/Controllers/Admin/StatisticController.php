<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Company;
use App\Models\Industry;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class StatisticController extends Controller
{
    private const MONTHS = 6;

    public function index(): View
    {
        $jobCounts = Job::toBase()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.statistics.index', [
            'cards' => [
                ['label' => 'Ứng viên', 'value' => User::where('role', User::ROLE_CANDIDATE)->count(), 'url' => route('admin.candidates.index')],
                ['label' => 'Nhà tuyển dụng', 'value' => User::where('role', User::ROLE_RECRUITER)->count(), 'url' => route('admin.recruiters.index')],
                ['label' => 'Công ty', 'value' => Company::count(), 'url' => null],
                ['label' => 'Tin tuyển dụng', 'value' => $jobCounts->sum(), 'url' => route('admin.jobs.index')],
                ['label' => 'Tin chờ duyệt', 'value' => (int) ($jobCounts[Job::STATUS_PENDING] ?? 0), 'url' => route('admin.jobs.index', ['status' => Job::STATUS_PENDING])],
                ['label' => 'Lượt ứng tuyển', 'value' => Application::count(), 'url' => null],
            ],
            'jobsByStatus' => $this->ranked(Job::STATUS_LABELS, $jobCounts),
            'applicationsByStatus' => $this->ranked(
                Application::STATUS_LABELS,
                Application::toBase()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            ),
            'topIndustries' => Industry::withCount('jobs')
                ->orderByDesc('jobs_count')
                ->orderBy('name')
                ->limit(5)
                ->get()
                ->map(fn (Industry $industry) => ['label' => $industry->name, 'value' => $industry->jobs_count]),
            'newUsersByMonth' => $this->perMonth(User::where('role', '!=', User::ROLE_ADMIN)),
            'newJobsByMonth' => $this->perMonth(Job::query()),
        ]);
    }

    /**
     * Mọi trạng thái (kể cả bằng 0), sắp xếp từ nhiều xuống ít để dễ so sánh.
     *
     * @param  array<string, string>  $labels
     */
    private function ranked(array $labels, Collection $counts): Collection
    {
        return collect($labels)
            ->map(fn (string $label, string $status) => ['label' => $label, 'value' => (int) ($counts[$status] ?? 0)])
            ->sortByDesc('value')
            ->values();
    }

    /**
     * Số bản ghi tạo mới theo tháng. Gom nhóm bằng PHP để chạy được trên cả MySQL và SQLite.
     */
    private function perMonth(Builder $query): Collection
    {
        $from = now()->startOfMonth()->subMonths(self::MONTHS - 1);

        $counts = $query->where('created_at', '>=', $from)
            ->pluck('created_at')
            ->countBy(fn ($createdAt) => $createdAt->format('Y-m'));

        return collect(range(0, self::MONTHS - 1))->map(function (int $offset) use ($from, $counts) {
            $month = $from->copy()->addMonths($offset);

            return ['label' => $month->format('m/y'), 'value' => $counts[$month->format('Y-m')] ?? 0];
        });
    }
}
