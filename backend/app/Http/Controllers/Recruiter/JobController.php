<?php

namespace App\Http\Controllers\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\Job;
use App\Models\Notification;
use App\Models\RecruiterProfile;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JobController extends Controller
{
    public function index(Request $request): View
    {
        $profile = $this->profile($request);
        $status = $request->query('status');
        $status = is_string($status) && array_key_exists($status, Job::STATUS_LABELS) ? $status : null;

        $jobsQuery = $profile->jobs();
        $counts = (clone $jobsQuery)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $jobs = $jobsQuery
            ->with('industry')
            ->withCount('applications')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('recruiter.jobs.index', [
            'jobs' => $jobs,
            'status' => $status,
            'statuses' => Job::STATUS_LABELS,
            'counts' => $counts,
            'company' => $profile->company,
        ]);
    }

    public function create(Request $request): View
    {
        $profile = $this->profile($request);

        return view('recruiter.jobs.create', [
            'job' => new Job(['quantity' => 1, 'salary_currency' => 'VND']),
            'company' => $profile->company,
            ...$this->formOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $profile = $this->profile($request);
        $data = $this->validated($request);

        $job = DB::transaction(function () use ($profile, $data) {
            $job = $profile->jobs()->create([
                ...collect($data)->except('skills')->all(),
                'company_id' => $profile->company_id,
                'status' => Job::STATUS_DRAFT,
            ]);

            $this->syncSkills($job, $data['skills'] ?? []);

            return $job;
        });

        return redirect()->route('recruiter.jobs.edit', $job)->with('success', 'Đã lưu tin ở trạng thái nháp. Kiểm tra lại nội dung rồi gửi duyệt.');
    }

    public function edit(Request $request, Job $job): View
    {
        $job = $this->ownedJob($request, $job);
        $profile = $this->profile($request);
        $job->load('skills');

        return view('recruiter.jobs.edit', [
            'job' => $job,
            'company' => $profile->company,
            ...$this->formOptions(),
        ]);
    }

    public function update(Request $request, Job $job): RedirectResponse
    {
        $job = $this->ownedJob($request, $job);
        $data = $this->validated($request);
        $needsReview = $job->status === Job::STATUS_APPROVED;

        DB::transaction(function () use ($job, $data, $needsReview) {
            $job->update([
                ...collect($data)->except('skills')->all(),
                ...($needsReview ? [
                    'status' => Job::STATUS_PENDING,
                    'reviewed_by' => null,
                    'reviewed_at' => null,
                    'rejection_reason' => null,
                ] : []),
            ]);

            $this->syncSkills($job, $data['skills'] ?? []);
        });

        $message = $needsReview
            ? 'Đã cập nhật tin. Tin đã chuyển về trạng thái chờ duyệt và tạm thời không hiển thị công khai.'
            : 'Đã cập nhật tin tuyển dụng.';

        return redirect()->route('recruiter.jobs.index')->with('success', $message);
    }

    public function submit(Request $request, Job $job): RedirectResponse
    {
        $job = $this->ownedJob($request, $job);

        return $this->transition(
            $request,
            $job,
            [Job::STATUS_DRAFT, Job::STATUS_REJECTED, Job::STATUS_HIDDEN, Job::STATUS_CLOSED],
            Job::STATUS_PENDING,
            'Đã gửi tin tuyển dụng đến quản trị viên để duyệt.',
            ['reviewed_by' => null, 'reviewed_at' => null, 'rejection_reason' => null],
        );
    }

    public function close(Request $request, Job $job): RedirectResponse
    {
        $job = $this->ownedJob($request, $job);

        return $this->transition(
            $request,
            $job,
            [Job::STATUS_PENDING, Job::STATUS_APPROVED],
            Job::STATUS_CLOSED,
            'Đã đóng tin tuyển dụng.',
        );
    }

    public function hide(Request $request, Job $job): RedirectResponse
    {
        $job = $this->ownedJob($request, $job);

        return $this->transition(
            $request,
            $job,
            [Job::STATUS_APPROVED],
            Job::STATUS_HIDDEN,
            'Đã ẩn tin tuyển dụng khỏi trang việc làm.',
        );
    }

    public function destroy(Request $request, Job $job): RedirectResponse
    {
        $job = $this->ownedJob($request, $job);

        if ($job->applications()->exists()) {
            return back()->with('error', 'Tin đã có ứng viên nộp hồ sơ nên không thể xóa. Bạn có thể đóng hoặc ẩn tin này.');
        }

        $job->delete();

        return redirect()->route('recruiter.jobs.index')->with('success', 'Đã xóa tin tuyển dụng.');
    }

    private function profile(Request $request): RecruiterProfile
    {
        $profile = $request->user()->recruiterProfile()->with('company')->first();
        abort_unless($profile?->company, 403, 'Tài khoản chưa được gắn với công ty. Vui lòng liên hệ quản trị viên.');

        return $profile;
    }

    private function ownedJob(Request $request, Job $job): Job
    {
        abort_unless((int) $job->created_by === (int) $request->user()->id, 404);

        return $job;
    }

    /** @return array<string, mixed> */
    private function formOptions(): array
    {
        return [
            'industries' => Industry::where('status', Industry::STATUS_ACTIVE)->orderBy('name')->pluck('name', 'id'),
            'skills' => Skill::orderBy('name')->pluck('name', 'id'),
        ];
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'industry_id' => ['required', 'integer', Rule::exists('industries', 'id')->where('status', Industry::STATUS_ACTIVE)],
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
            'skills.*' => ['integer', 'distinct', 'exists:skills,id'],
        ]);
    }

    /** @param array<int, int|string> $skillIds */
    private function syncSkills(Job $job, array $skillIds): void
    {
        $job->skills()->sync(
            collect($skillIds)->mapWithKeys(fn ($id) => [(int) $id => ['is_required' => true, 'created_at' => now()]])->all()
        );
    }

    /** @param array<int, string> $from */
    private function transition(Request $request, Job $job, array $from, string $to, string $message, array $extra = []): RedirectResponse
    {
        $changed = DB::transaction(function () use ($request, $job, $from, $to, $extra) {
            $current = Job::whereKey($job->id)->lockForUpdate()->firstOrFail();
            abort_unless((int) $current->created_by === (int) $request->user()->id, 404);

            if (! in_array($current->status, $from, true)) {
                return false;
            }

            $current->update(['status' => $to, ...$extra]);

            if ($to === Job::STATUS_PENDING) {
                $adminIds = User::query()
                    ->where('role', User::ROLE_ADMIN)
                    ->where('status', User::STATUS_ACTIVE)
                    ->pluck('id');

                foreach ($adminIds as $adminId) {
                    Notification::create([
                        'user_id' => $adminId,
                        'type' => 'job.pending_review',
                        'title' => 'Tin tuyển dụng cần duyệt',
                        'content' => 'Tin "'.$current->title.'" đã được gửi lên và đang chờ admin xét duyệt.',
                        'data' => ['job_id' => $current->id],
                    ]);
                }
            }

            return true;
        });

        return $changed
            ? back()->with('success', $message)
            : back()->with('error', 'Trạng thái tin đã thay đổi, vui lòng tải lại trang.');
    }
}
