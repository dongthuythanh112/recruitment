<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Industry;
use App\Models\Job;
use App\Models\Skill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JobController extends Controller
{
    public function index(Request $request): View
    {
        $jobs = Job::with(['company', 'industry', 'creator'])
            ->withCount('applications')
            ->when($request->query('q'), fn ($query, $keyword) => $query->where('title', 'like', "%{$keyword}%"))
            ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->query('industry_id'), fn ($query, $industryId) => $query->where('industry_id', $industryId))
            ->when($request->query('company_id'), fn ($query, $companyId) => $query->where('company_id', $companyId))
            ->when($request->query('created_by'), fn ($query, $userId) => $query->where('created_by', $userId))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.jobs.index', [
            'jobs' => $jobs,
            'industries' => Industry::orderBy('name')->pluck('name', 'id'),
            'companies' => Company::orderBy('name')->pluck('name', 'id'),
        ]);
    }

    public function show(Job $job): View
    {
        $job->load(['company', 'industry', 'creator', 'reviewer', 'skills', 'moderationHistories' => fn ($query) => $query->with('admin')->latest()])
            ->loadCount('applications');

        return view('admin.jobs.show', compact('job'));
    }

    public function create(): View
    {
        return view('admin.jobs.create', ['job' => new Job, ...$this->formOptions()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $job = DB::transaction(function () use ($request, $data) {
            // Tin do admin tạo vẫn đi qua hàng đợi duyệt như mọi tin khác.
            $job = Job::create([
                ...collect($data)->except('skills')->all(),
                'created_by' => $request->user()->id,
                'status' => Job::STATUS_PENDING,
            ]);
            $this->syncSkills($job, $data['skills'] ?? []);

            return $job;
        });

        return redirect()->route('admin.jobs.show', $job)->with('success', 'Đã tạo tin tuyển dụng, tin đang chờ duyệt.');
    }

    public function edit(Job $job): View
    {
        $job->load('skills');

        return view('admin.jobs.edit', ['job' => $job, ...$this->formOptions()]);
    }

    public function update(Request $request, Job $job): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($job, $data) {
            $job->update(collect($data)->except('skills')->all());
            $this->syncSkills($job, $data['skills'] ?? []);
        });

        return redirect()->route('admin.jobs.show', $job)->with('success', 'Đã cập nhật tin tuyển dụng.');
    }

    public function destroy(Job $job): RedirectResponse
    {
        if ($job->applications()->exists()) {
            return back()->with('error', 'Tin đã có người ứng tuyển nên không thể xóa. Hãy ẩn tin thay vì xóa.');
        }

        $job->delete();

        return redirect()->route('admin.jobs.index')->with('success', 'Đã xóa tin tuyển dụng.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'companies' => Company::orderBy('name')->pluck('name', 'id'),
            'industries' => Industry::orderBy('name')->pluck('name', 'id'),
            'skills' => Skill::orderBy('name')->pluck('name', 'id'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'industry_id' => ['required', 'integer', 'exists:industries,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'benefits' => ['nullable', 'string'],
            'employment_type' => ['nullable', Rule::in(array_keys(Job::EMPLOYMENT_TYPE_LABELS))],
            'workplace_type' => ['nullable', Rule::in(array_keys(Job::WORKPLACE_TYPE_LABELS))],
            'location' => ['nullable', 'string', 'max:500'],
            'salary_min' => ['nullable', 'numeric', 'min:0', 'max:9999999999999'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'max:9999999999999', Rule::when($request->filled('salary_min'), 'gte:salary_min')],
            'salary_currency' => ['nullable', 'string', 'max:10'],
            'experience_years' => ['nullable', 'numeric', 'min:0', 'max:99'],
            'quantity' => ['required', 'integer', 'min:1'],
            'deadline' => ['nullable', 'date'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', 'exists:skills,id'],
        ]);
    }

    /**
     * @param  array<int, int|string>  $skillIds
     */
    private function syncSkills(Job $job, array $skillIds): void
    {
        $job->skills()->sync(
            collect($skillIds)->mapWithKeys(fn ($id) => [(int) $id => ['is_required' => true, 'created_at' => now()]])->all()
        );
    }
}
