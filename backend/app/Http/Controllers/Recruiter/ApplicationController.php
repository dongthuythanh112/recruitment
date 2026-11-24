<?php

namespace App\Http\Controllers\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Resume;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $status = $request->query('status');
        $status = is_string($status) && array_key_exists($status, Application::STATUS_LABELS) ? $status : null;

        $jobId = $request->query('job_id');
        $jobId = (is_string($jobId) && ctype_digit($jobId) && $request->user()->createdJobs()->whereKey($jobId)->exists())
            ? (int) $jobId
            : null;

        $applicationsQuery = Application::query()
            ->whereHas('job', fn ($query) => $query->where('created_by', $userId)
                ->when($jobId, fn ($query) => $query->whereKey($jobId)));

        $counts = (clone $applicationsQuery)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $applications = $applicationsQuery
            ->with(['job', 'candidate.user', 'resume'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest('applied_at')
            ->paginate(12)
            ->withQueryString();

        return view('recruiter.applications.index', [
            'applications' => $applications,
            'counts' => $counts,
            'status' => $status,
            'statuses' => Application::STATUS_LABELS,
            'jobs' => $request->user()->createdJobs()->orderBy('title')->pluck('title', 'id'),
            'jobId' => $jobId,
        ]);
    }

    public function show(Request $request, Application $application): View
    {
        $application = $this->ownedApplication($request, $application);
        $application->load([
            'job.company',
            'candidate.user',
            'candidate.resumes',
            'candidate.educations' => fn ($query) => $query->orderByDesc('start_date'),
            'candidate.experiences' => fn ($query) => $query->orderByDesc('start_date'),
            'candidate.skills',
            'resume',
            'statusHistories' => fn ($query) => $query->with('changer')->latest('created_at'),
        ]);
        $resumeAvailability = $application->candidate->resumes
            ->mapWithKeys(fn (Resume $resume) => [$resume->id => $this->findResumeDisk($resume) !== null]);

        return view('recruiter.applications.show', [
            'application' => $application,
            'statusLabels' => Application::STATUS_LABELS,
            'editableStatuses' => Application::RECRUITER_STATUS_LABELS,
            'resumeAvailability' => $resumeAvailability,
        ]);
    }

    public function updateStatus(Request $request, Application $application): RedirectResponse
    {
        $application = $this->ownedApplication($request, $application);

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Application::RECRUITER_STATUS_LABELS))],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $result = DB::transaction(function () use ($request, $application, $data) {
            $current = Application::whereKey($application->id)->lockForUpdate()->firstOrFail();

            abort_unless(
                $current->job()->where('created_by', $request->user()->id)->exists(),
                404,
            );

            if ($current->status === $data['status']) {
                return 'unchanged';
            }

            if ($current->status === 'withdrawn') {
                return 'withdrawn';
            }

            $oldStatus = $current->status;
            $current->update(['status' => $data['status']]);
            $current->statusHistories()->create([
                'old_status' => $oldStatus,
                'new_status' => $data['status'],
                'changed_by' => $request->user()->id,
                'note' => $data['note'] ?? null,
            ]);

            return 'changed';
        });

        $message = match ($result) {
            'changed' => 'Đã cập nhật trạng thái và ghi nhận lịch sử xử lý.',
            'withdrawn' => 'Ứng viên đã rút hồ sơ; không thể cập nhật trạng thái này.',
            default => 'Hồ sơ đã ở trạng thái này, không có thay đổi nào được ghi nhận.',
        };

        return redirect()->route('recruiter.applications.show', $application)
            ->with($result === 'withdrawn' ? 'error' : 'success', $message);
    }

    public function viewResume(Request $request, Application $application, Resume $resume): StreamedResponse
    {
        $application = $this->ownedApplication($request, $application);
        $this->assertResumeBelongsToCandidate($application, $resume);

        $filename = $this->resumeFilename($resume);
        abort_unless(strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'pdf', 404);

        $disk = $this->resumeDisk($resume);

        return Storage::disk($disk)->response($resume->file_path, $filename, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
        ]);
    }

    public function downloadResume(Request $request, Application $application, Resume $resume): StreamedResponse
    {
        $application = $this->ownedApplication($request, $application);
        $this->assertResumeBelongsToCandidate($application, $resume);

        $disk = $this->resumeDisk($resume);

        return Storage::disk($disk)->download($resume->file_path, $this->resumeFilename($resume), [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function ownedApplication(Request $request, Application $application): Application
    {
        abort_unless(
            $application->job()->where('created_by', $request->user()->id)->exists(),
            404,
        );

        return $application;
    }

    private function assertResumeBelongsToCandidate(Application $application, Resume $resume): void
    {
        abort_unless((int) $resume->candidate_id === (int) $application->candidate_id, 404);
    }

    private function resumeDisk(Resume $resume): string
    {
        $disk = $this->findResumeDisk($resume);
        abort_unless($disk, 404, 'Không tìm thấy tệp CV.');

        return $disk;
    }

    private function findResumeDisk(Resume $resume): ?string
    {
        $path = $resume->file_path;

        if (! is_string($path)
            || $path === ''
            || preg_match('#^(?:[A-Za-z]:|[/\\\\])#', $path)
            || preg_match('#(^|[/\\\\])\.\.([/\\\\]|$)#', $path)) {
            return null;
        }

        $disks = array_unique([config('filesystems.default'), 'local', 'public']);

        foreach ($disks as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return $disk;
            }
        }

        return null;
    }

    private function resumeFilename(Resume $resume): string
    {
        $filename = basename(str_replace('\\', '/', $resume->original_filename ?: $resume->file_path));

        return $filename !== '' ? $filename : 'CV';
    }
}
