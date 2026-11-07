<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\Industry;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        // Trang công khai: tham số dạng mảng (?q[]=x) không được lọt xuống câu truy vấn.
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:100'],
            'industry_id' => ['nullable', 'integer'],
        ]);
        $industry = isset($filters['industry_id']) ? Industry::find($filters['industry_id']) : null;

        $jobs = Job::published()
            ->with('company')
            ->when($filters['q'] ?? null, fn ($query, $keyword) => $query->where(fn ($query) => $query
                ->where('title', 'like', "%{$keyword}%")
                ->orWhereHas('company', fn ($query) => $query->where('name', 'like', "%{$keyword}%"))))
            ->when($filters['location'] ?? null, fn ($query, $location) => $query->where('location', 'like', "%{$location}%"))
            ->when($industry, fn ($query) => $query->whereIn('industry_id', $industry->selfAndDescendantIds()))
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString()
            ->fragment('viec-lam');

        return view('client.home', [
            'jobs' => $jobs,
            'isFiltered' => array_filter($filters) !== [],
            'openJobsCount' => Job::published()->count(),
            'hiringCompaniesCount' => Job::published()->distinct()->count('company_id'),
            'candidatesCount' => CandidateProfile::count(),
            'industryOptions' => Industry::where('status', Industry::STATUS_ACTIVE)->orderBy('name')->pluck('name', 'id'),
            'industries' => $this->topIndustries(),
            'companies' => Company::whereHas('jobs', fn ($query) => $query->published())
                ->withCount(['jobs as open_jobs_count' => fn ($query) => $query->published()])
                ->orderByDesc('open_jobs_count')
                ->orderBy('name')
                ->limit(6)
                ->get(),
        ]);
    }

    /**
     * Ngành gốc có nhiều việc nhất; việc của ngành con được cộng dồn lên ngành gốc.
     *
     * @return Collection<int, Industry>
     */
    private function topIndustries(): Collection
    {
        $counts = Job::published()->toBase()->selectRaw('industry_id, count(*) as total')->groupBy('industry_id')->pluck('total', 'industry_id');
        $industries = Industry::where('status', Industry::STATUS_ACTIVE)->get()->keyBy('id');

        foreach ($counts as $industryId => $total) {
            $root = $industries->get($industryId);

            while ($root && $industries->has($root->parent_id)) {
                $root = $industries->get($root->parent_id);
            }

            if ($root) {
                $root->open_jobs_count = ($root->open_jobs_count ?? 0) + $total;
            }
        }

        return $industries->filter(fn (Industry $industry) => $industry->open_jobs_count > 0)
            ->sortByDesc('open_jobs_count')
            ->take(8)
            ->values();
    }
}
