<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $candidate = $this->candidate($request);
        $applicationsQuery = $candidate->applications()
            ->with([
                'job.company',
                'resume',
                'statusHistories' => fn ($query) => $query->with('changer')->orderBy('created_at'),
            ]);

        if ($request->filled('application_id') && ctype_digit((string) $request->query('application_id'))) {
            $applicationsQuery->whereKey((int) $request->query('application_id'));
        }

        $applications = $applicationsQuery->latest('applied_at')
            ->paginate(10);

        return view('client.applications.index', compact('applications'));
    }

    public function create(Request $request, Job $job): View|RedirectResponse
    {
        $candidate = $this->candidate($request);
        abort_unless(Job::published()->whereKey($job->id)->exists(), 404);

        $existing = $candidate->applications()->where('job_id', $job->id)->first();
        if ($existing && $existing->status !== 'withdrawn') {
            return redirect()->route('candidate.applications.index')->with('error', 'Bạn đã ứng tuyển vị trí này rồi.');
        }

        $resumes = $candidate->resumes()->latest()->get();

        return view('client.applications.create', [
            'job' => $job->load('company', 'industry'),
            'resumes' => $resumes,
            'defaultResumeId' => $resumes->firstWhere('is_default', true)?->id ?? $resumes->first()?->id,
            'existing' => $existing,
        ]);
    }

    public function store(Request $request, Job $job): RedirectResponse
    {
        $candidate = $this->candidate($request);
        $data = $request->validate([
            'resume_id' => ['required', 'integer', 'exists:resumes,id'],
            'cover_letter' => ['nullable', 'string', 'max:5000'],
        ], [
            'resume_id.required' => 'Vui lòng chọn CV muốn gửi cho nhà tuyển dụng.',
        ]);
        $resume = $candidate->resumes()->whereKey($data['resume_id'])->firstOrFail();

        $result = DB::transaction(function () use ($request, $candidate, $job, $resume, $data): string {
            $publishedJob = Job::published()->whereKey($job->id)->lockForUpdate()->firstOrFail();
            $application = Application::where('job_id', $publishedJob->id)
                ->where('candidate_id', $candidate->id)
                ->lockForUpdate()
                ->first();

            if ($application && $application->status !== 'withdrawn') {
                return 'duplicate';
            }

            if ($application) {
                $oldStatus = $application->status;
                $application->update([
                    'resume_id' => $resume->id,
                    'cover_letter' => $data['cover_letter'] ?? null,
                    'status' => 'pending',
                ]);
                $application->applied_at = now();
                $application->save();
            } else {
                $oldStatus = null;
                $application = Application::create([
                    'job_id' => $publishedJob->id,
                    'candidate_id' => $candidate->id,
                    'resume_id' => $resume->id,
                    'cover_letter' => $data['cover_letter'] ?? null,
                    'status' => 'pending',
                ]);
            }

            $application->statusHistories()->create([
                'old_status' => $oldStatus,
                'new_status' => 'pending',
                'changed_by' => $request->user()->id,
                'note' => $oldStatus === 'withdrawn' ? 'Ứng viên gửi lại hồ sơ ứng tuyển.' : 'Ứng viên nộp hồ sơ.',
            ]);

            Notification::create([
                'user_id' => $publishedJob->created_by,
                'type' => 'application.submitted',
                'title' => 'Có ứng viên mới ứng tuyển',
                'content' => $request->user()->name.' đã ứng tuyển vị trí "'.$publishedJob->title.'".',
                'data' => [
                    'application_id' => $application->id,
                    'job_id' => $publishedJob->id,
                ],
            ]);

            return 'submitted';
        });

        if ($result === 'duplicate') {
            return redirect()->route('candidate.applications.index')->with('error', 'Bạn đã ứng tuyển vị trí này rồi.');
        }

        return redirect()->route('candidate.applications.index')->with('success', 'Đã gửi hồ sơ ứng tuyển thành công.');
    }

    public function withdraw(Request $request, Application $application): RedirectResponse
    {
        $candidate = $this->candidate($request);
        abort_unless((int) $application->candidate_id === (int) $candidate->id, 404);

        $result = DB::transaction(function () use ($candidate, $application, $request): string {
            $current = Application::whereKey($application->id)->lockForUpdate()->firstOrFail();
            abort_unless((int) $current->candidate_id === (int) $candidate->id, 404);

            if (in_array($current->status, ['withdrawn', 'rejected', 'hired'], true)) {
                return 'unchangeable';
            }

            $oldStatus = $current->status;
            $current->update(['status' => 'withdrawn']);
            $current->statusHistories()->create([
                'old_status' => $oldStatus,
                'new_status' => 'withdrawn',
                'changed_by' => $request->user()->id,
                'note' => 'Ứng viên đã rút hồ sơ.',
            ]);

            $job = $current->job()->first();
            Notification::create([
                'user_id' => $job->created_by,
                'type' => 'application.withdrawn',
                'title' => 'Ứng viên đã rút hồ sơ',
                'content' => $request->user()->name.' đã rút hồ sơ ứng tuyển vị trí "'.$job->title.'".',
                'data' => [
                    'application_id' => $current->id,
                    'job_id' => $job->id,
                ],
            ]);

            return 'withdrawn';
        });

        return back()->with(
            $result === 'withdrawn' ? 'success' : 'error',
            $result === 'withdrawn' ? 'Bạn đã rút hồ sơ ứng tuyển.' : 'Không thể rút hồ sơ ở trạng thái hiện tại.',
        );
    }

    private function candidate(Request $request): \App\Models\CandidateProfile
    {
        abort_unless(
            $request->user()?->role === User::ROLE_CANDIDATE
                && $request->user()?->status === User::STATUS_ACTIVE,
            403,
            'Chức năng này chỉ dành cho ứng viên đang hoạt động.',
        );

        return $request->user()->candidateProfile()->firstOrCreate();
    }
}
