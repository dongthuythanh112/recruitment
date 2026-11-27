<?php

namespace App\Http\Controllers\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class StatisticController extends Controller
{
    public function index(Request $request): View
    {
        $profile = $request->user()->recruiterProfile()->with('company')->first();
        abort_unless($profile?->company, 403, 'Tài khoản chưa được gắn với công ty. Vui lòng liên hệ quản trị viên.');

        $jobsQuery = $profile->jobs();
        $applicationsQuery = Application::query()->whereHas('job', fn ($query) => $query->where('created_by', $profile->user_id));
        $jobCounts = (clone $jobsQuery)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $applicationsByStatus = (clone $applicationsQuery)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $from = now()->startOfMonth()->subMonths(5);
        $monthlyCounts = (clone $applicationsQuery)->where('applied_at', '>=', $from)
            ->get(['applied_at'])->groupBy(fn (Application $application) => $application->applied_at->format('Y-m'))
            ->map->count();
        $monthlyApplications = collect(range(5, 0))->map(function (int $offset) use ($monthlyCounts): array {
            $month = now()->startOfMonth()->subMonths($offset);

            return ['label' => $month->translatedFormat('M'), 'total' => (int) ($monthlyCounts[$month->format('Y-m')] ?? 0)];
        });

        $totalApplications = (int) $applicationsByStatus->sum();
        $topJobs = (clone $jobsQuery)->select(['id', 'title', 'status'])->withCount('applications')
            ->orderByDesc('applications_count')->latest()->limit(5)->get();
        $maxMonthlyApplications = max(1, (int) $monthlyApplications->max('total'));

        return view('recruiter.statistics.index', [
            'company' => $profile->company,
            'totalJobs' => (clone $jobsQuery)->count(),
            'publishedJobs' => (clone $jobsQuery)->where('status', Job::STATUS_APPROVED)->count(),
            'pendingJobs' => (clone $jobsQuery)->where('status', Job::STATUS_PENDING)->count(),
            'totalApplications' => $totalApplications,
            'hiredApplications' => (int) ($applicationsByStatus['hired'] ?? 0),
            'applicationsByStatus' => $applicationsByStatus,
            'monthlyApplications' => $monthlyApplications,
            'maxMonthlyApplications' => $maxMonthlyApplications,
            'topJobs' => $topJobs,
            'statuses' => Application::STATUS_LABELS,
            'jobCounts' => $jobCounts,
        ]);
    }
}
