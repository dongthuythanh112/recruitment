<?php

namespace App\Http\Controllers\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use App\Models\Resume;
use App\Services\CandidateSearchability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CandidateSearchController extends Controller
{
    public function index(Request $request, CandidateSearchability $searchability): View
    {
        $candidates = CandidateProfile::query()
            ->whereIn('id', $searchability->candidateIds())
            ->whereHas('user', fn ($query) => $query->where('status', 'active'))
            ->with(['user', 'skills'])
            ->withCount('skills')
            ->latest('updated_at')
            ->paginate(12);

        return view('recruiter.candidates.index', compact('candidates'));
    }

    public function show(Request $request, CandidateProfile $candidate, CandidateSearchability $searchability): View
    {
        $candidate = $this->visibleCandidate($candidate, $searchability);
        $candidate->load([
            'user',
            'skills',
            'educations' => fn ($query) => $query->orderByDesc('start_date'),
            'experiences' => fn ($query) => $query->orderByDesc('start_date'),
            'resumes' => fn ($query) => $query->latest(),
        ]);

        return view('recruiter.candidates.show', compact('candidate'));
    }

    public function viewResume(
        Request $request,
        CandidateProfile $candidate,
        Resume $resume,
        CandidateSearchability $searchability,
    ): StreamedResponse {
        $candidate = $this->visibleCandidate($candidate, $searchability);
        $this->assertResumeBelongsToCandidate($candidate, $resume);
        $filename = $this->resumeFilename($resume);
        abort_unless(strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'pdf', 404);

        return Storage::disk($this->resumeDisk($resume))->response($resume->file_path, $filename, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
        ]);
    }

    public function downloadResume(
        Request $request,
        CandidateProfile $candidate,
        Resume $resume,
        CandidateSearchability $searchability,
    ): StreamedResponse {
        $candidate = $this->visibleCandidate($candidate, $searchability);
        $this->assertResumeBelongsToCandidate($candidate, $resume);

        return Storage::disk($this->resumeDisk($resume))->download($resume->file_path, $this->resumeFilename($resume), [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function visibleCandidate(CandidateProfile $candidate, CandidateSearchability $searchability): CandidateProfile
    {
        abort_unless(
            $searchability->isEnabled((int) $candidate->id)
                && $candidate->user()->where('status', 'active')->exists(),
            404,
        );

        return $candidate;
    }

    private function assertResumeBelongsToCandidate(CandidateProfile $candidate, Resume $resume): void
    {
        abort_unless((int) $resume->candidate_id === (int) $candidate->id, 404);
    }

    private function resumeDisk(Resume $resume): string
    {
        $path = $resume->file_path;
        abort_unless(
            is_string($path)
                && $path !== ''
                && ! preg_match('#^(?:[A-Za-z]:|[/\\\\])#', $path)
                && ! preg_match('#(^|[/\\\\])\.\.([/\\\\]|$)#', $path),
            404,
        );

        foreach (array_unique([config('filesystems.default'), 'local', 'public']) as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return $disk;
            }
        }

        abort(404);
    }

    private function resumeFilename(Resume $resume): string
    {
        $filename = basename(str_replace('\\', '/', $resume->original_filename ?: $resume->file_path));

        return $filename !== '' ? $filename : 'CV.pdf';
    }
}
