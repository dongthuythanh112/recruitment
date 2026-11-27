<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\Industry;
use App\Models\Job;
use App\Models\User;
use App\Services\JobRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const PROVINCES = [
        'An Giang', 'Bắc Ninh', 'Cà Mau', 'Cao Bằng', 'Đắk Lắk', 'Điện Biên',
        'Đồng Nai', 'Đồng Tháp', 'Gia Lai', 'Hà Nội', 'Hà Tĩnh', 'Hải Phòng',
        'Hồ Chí Minh', 'Huế', 'Hưng Yên', 'Khánh Hòa', 'Lai Châu', 'Lâm Đồng',
        'Lạng Sơn', 'Lào Cai', 'Nghệ An', 'Ninh Bình', 'Phú Thọ', 'Quảng Ngãi',
        'Quảng Ninh', 'Quảng Trị', 'Sơn La', 'Tây Ninh', 'Thái Nguyên', 'Thanh Hóa',
        'Tuyên Quang', 'Vĩnh Long', 'Cần Thơ', 'Đà Nẵng',
    ];
    public function index(Request $request, JobRecommendationService $recommendations): View
    {
        // Trang công khai: tham số dạng mảng (?q[]=x) không được lọt xuống câu truy vấn.
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:100'],
            'industry_id' => ['nullable', 'integer'],
        ]);
        $industry = isset($filters['industry_id']) ? Industry::find($filters['industry_id']) : null;
        $candidate = $request->user()?->role === User::ROLE_CANDIDATE
            && $request->user()?->status === User::STATUS_ACTIVE
                ? ($request->user()->candidateProfile()->with('skills')->first() ?? new CandidateProfile())
                : null;
        $jobSuggestions = $recommendations->recommend($candidate, PHP_INT_MAX);
        $savedJobIds = $candidate && $candidate->exists
            ? $candidate->savedJobs()->pluck('jobs.id')->map(fn ($id) => (int) $id)->all()
            : [];
        $homeSuggestions = $jobSuggestions['matched']->map(fn (array $suggestion) => [
            'job' => $suggestion['job'],
            'reasons' => $suggestion['reasons'],
        ])->concat($jobSuggestions['featured']->map(fn (Job $job) => ['job' => $job, 'reasons' => []]))
            ->sortByDesc(fn (array $suggestion) => $suggestion['job']->published_at?->timestamp ?? 0)
            ->take(3)
            ->values();

        $searchJobs = Job::published()
            ->with('company')
            ->when($filters['q'] ?? null, fn ($query, $keyword) => $query->where(fn ($query) => $query
                ->where('title', 'like', "%{$keyword}%")
                ->orWhereHas('company', fn ($query) => $query->where('name', 'like', "%{$keyword}%"))))
            ->when($filters['location'] ?? null, fn ($query, $location) => $query->where('location', 'like', "%{$location}%"))
            ->when($industry, fn ($query) => $query->whereIn('industry_id', $industry->selfAndDescendantIds()))
            ->latest('published_at')
            ->limit(8)
            ->get();
        $featuredJobs = Job::published()
            ->with('company')
            ->latest('published_at')
            ->limit(9)
            ->get();

        return view('client.home', [
            'searchJobs' => $searchJobs,
            'featuredJobs' => $featuredJobs,
            'candidate' => $candidate,
            'savedJobIds' => $savedJobIds,
            'homeSuggestions' => $homeSuggestions,
            'isFiltered' => array_filter($filters) !== [],
            'openJobsCount' => Job::published()->count(),
            'hiringCompaniesCount' => Job::published()->distinct()->count('company_id'),
            'candidatesCount' => CandidateProfile::count(),
            'industryOptions' => Industry::where('status', Industry::STATUS_ACTIVE)->orderBy('name')->pluck('name', 'id'),
            'provinceOptions' => self::PROVINCES,
            'industries' => $this->topIndustries(),
            'companies' => Company::whereHas('jobs', fn ($query) => $query->published())
                ->withCount(['jobs as open_jobs_count' => fn ($query) => $query->published()])
                ->orderByDesc('open_jobs_count')
                ->orderBy('name')
                ->limit(10)
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
            ->take(16)
            ->each(fn (Industry $industry) => $industry->setAttribute('home_icon', $this->industryIcon($industry->name)))
            ->values();
    }

    private function industryIcon(string $name): string
    {
        $name = Str::ascii(mb_strtolower($name));

        return match (true) {
            Str::contains($name, ['cong nghe', 'phan mem', 'lap trinh', 'it ', 'du lieu', 'data']) => 'technology',
            Str::contains($name, ['tai chinh', 'ke toan', 'ngan hang', 'bao hiem']) => 'finance',
            Str::contains($name, ['marketing', 'truyen thong', 'quang cao', 'pr ', 'content']) => 'marketing',
            Str::contains($name, ['thiet ke', 'design', 'my thuat', 'sang tao']) => 'design',
            Str::contains($name, ['y te', 'duoc', 'dieu duong', 'suc khoe', 'bac si']) => 'health',
            Str::contains($name, ['giao duc', 'dao tao', 'giang day', 'gia su']) => 'education',
            Str::contains($name, ['van tai', 'logistics', 'giao nhan', 'kho van']) => 'logistics',
            Str::contains($name, ['nha hang', 'khach san', 'du lich', 'am thuc']) => 'hospitality',
            Str::contains($name, ['nhan su', 'hanh chinh', 'tuyen dung']) => 'people',
            Str::contains($name, ['phap ly', 'luat']) => 'legal',
            Str::contains($name, ['xay dung', 'bat dong san', 'kien truc']) => 'building',
            Str::contains($name, ['co khi', 'san xuat', 'tu dong hoa', 'ky thuat']) => 'engineering',
            Str::contains($name, ['kinh doanh', 'ban hang', 'thuong mai']) => 'sales',
            Str::contains($name, ['khoa hoc', 'nghien cuu', 'hoa hoc']) => 'science',
            default => 'briefcase',
        };
    }
}
