<?php

namespace App\Http\Controllers\Recruiter;

use App\Http\Controllers\Controller;
use App\Models\CandidateProfile;
use App\Models\Job;
use App\Models\Resume;
use App\Services\CandidateSearchability;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CandidateSearchController extends Controller
{
    public function index(Request $request, CandidateSearchability $searchability): View
    {
        $data = $request->validate([
            'job_id' => ['nullable', 'integer', Rule::exists('jobs', 'id')->where(fn ($query) => $query
                ->where('created_by', $request->user()->id)
                ->where('status', Job::STATUS_APPROVED)
                ->where(fn ($query) => $query->whereNull('deadline')->orWhereDate('deadline', '>=', today())))],
            'position' => ['nullable', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:150'],
            'experience_min' => ['nullable', 'numeric', 'min:0', 'max:80'],
            'salary_min' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'salary_max' => ['nullable', 'numeric', 'min:0', 'max:999999999999', 'gte:salary_min'],
        ]);

        $jobs = $request->user()->createdJobs()
            ->published()
            ->with(['skills', 'industry'])
            ->orderBy('title')
            ->get(['id', 'industry_id', 'title', 'location', 'experience_years', 'salary_min', 'salary_max', 'status', 'published_at', 'deadline']);
        $hasManualCriteria = collect($data)->except('job_id')->contains(fn ($value) => $value !== null && $value !== '');
        $selectedJob = isset($data['job_id']) ? $jobs->firstWhere('id', (int) $data['job_id']) : null;
        $comparisonJobs = $selectedJob ? collect([$selectedJob]) : $jobs;
        $filters = [
            'job_id' => $selectedJob?->id ?? '',
            'position' => trim($data['position'] ?? '') ?: ($selectedJob?->title ?? ''),
            'location' => trim($data['location'] ?? '') ?: ($selectedJob?->location ?? ''),
            'experience_min' => $data['experience_min'] ?? $selectedJob?->experience_years,
            'salary_min' => $data['salary_min'] ?? $selectedJob?->salary_min,
            'salary_max' => $data['salary_max'] ?? $selectedJob?->salary_max,
        ];
        $hasCriteria = $hasManualCriteria || $jobs->isNotEmpty();

        $profiles = CandidateProfile::query()
            ->whereIn('id', $searchability->candidateIds())
            ->whereHas('user', fn ($query) => $query->where('status', 'active'))
            ->with(['user', 'skills', 'experiences'])
            ->withCount('skills')
            ->latest('updated_at')
            ->get();

        $rankedCandidates = $profiles
            ->map(function (CandidateProfile $candidate) use ($filters, $hasCriteria, $comparisonJobs): ?CandidateProfile {
                $bestMatch = $comparisonJobs
                    ->map(function (Job $job) use ($candidate, $filters): ?array {
                        $jobPosition = trim($job->title.' '.$job->industry?->name);
                        if (! $this->positionMatches($candidate, $jobPosition)) {
                            return null;
                        }
                        if (filled($filters['position']) && ! $this->positionMatches($candidate, $filters['position'])) {
                            return null;
                        }

                        $locations = collect([$job->location, $filters['location']])->filter()->unique();
                        foreach ($locations as $location) {
                            if (filled($candidate->address) && ! $this->locationMatches($candidate->address, $location)) {
                                return null;
                            }
                        }

                        $candidateExperience = $candidate->years_of_experience !== null
                            ? (float) $candidate->years_of_experience
                            : null;
                        $requiredExperience = collect([$job->experience_years, $filters['experience_min']])
                            ->filter(fn ($value) => $value !== null && $value !== '')
                            ->map(fn ($value) => (float) $value)
                            ->max();
                        if ($requiredExperience !== null && $candidateExperience !== null && $candidateExperience < $requiredExperience) {
                            return null;
                        }

                        if (! $this->salaryMatches($candidate, $job->salary_min, $job->salary_max)
                            || ! $this->salaryMatches($candidate, $filters['salary_min'], $filters['salary_max'])) {
                            return null;
                        }

                        $reasons = ['Vị trí mong muốn phù hợp'];
                        $score = 3;
                        if ($locations->isNotEmpty() && filled($candidate->address)) {
                            $reasons[] = 'Địa điểm phù hợp';
                            $score += 2;
                        }
                        if ($requiredExperience !== null && $candidateExperience !== null) {
                            $reasons[] = 'Đạt kinh nghiệm yêu cầu';
                            $score += 2;
                        }
                        if (($job->salary_min !== null || $job->salary_max !== null || $filters['salary_min'] !== null || $filters['salary_max'] !== null)
                            && ($candidate->desired_salary_min !== null || $candidate->desired_salary_max !== null)) {
                            $reasons[] = 'Mức lương mong muốn phù hợp';
                            $score += 2;
                        }

                        $jobSkillNames = $job->skills->map(fn ($skill) => $this->normalize($skill->name))->all();
                        $matchedSkills = $candidate->skills->filter(fn ($skill) => in_array($this->normalize($skill->name), $jobSkillNames, true));
                        if ($matchedSkills->isNotEmpty()) {
                            $reasons[] = 'Khớp kỹ năng: '.$matchedSkills->take(2)->pluck('name')->implode(', ');
                            $score += min(2, $matchedSkills->count());
                        }

                        return ['job' => $job, 'score' => $score, 'reasons' => $reasons];
                    })
                    ->filter()
                    ->sortByDesc('score')
                    ->first();

                if (! $bestMatch) {
                    return null;
                }

                $candidate->recommendation_score = $bestMatch['score'];
                $candidate->recommendation_reasons = [...$bestMatch['reasons'], 'Phù hợp với tin: '.$bestMatch['job']->title];
                $candidate->profile_completeness = (int) filled($candidate->desired_position)
                    + (int) filled($candidate->address)
                    + (int) ($candidate->years_of_experience !== null)
                    + (int) ($candidate->desired_salary_min !== null || $candidate->desired_salary_max !== null)
                    + (int) ($candidate->skills_count > 0);

                return $candidate;
            })
            ->filter()
            ->sort(function (CandidateProfile $a, CandidateProfile $b) use ($hasCriteria): int {
                if ($hasCriteria && $a->recommendation_score !== $b->recommendation_score) {
                    return $b->recommendation_score <=> $a->recommendation_score;
                }

                return ($b->profile_completeness <=> $a->profile_completeness)
                    ?: (($b->updated_at?->timestamp ?? 0) <=> ($a->updated_at?->timestamp ?? 0));
            })
            ->values();
        $perPage = 12;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $candidates = new LengthAwarePaginator(
            $rankedCandidates->forPage($page, $perPage)->values(),
            $rankedCandidates->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return view('recruiter.candidates.index', compact('candidates', 'jobs', 'selectedJob', 'filters', 'hasCriteria'));
    }

    private function positionMatches(CandidateProfile $candidate, string $position): bool
    {
        $candidateText = $this->normalize(implode(' ', [
            $candidate->desired_position ?? '',
            $candidate->skills->pluck('name')->implode(' '),
            $candidate->experiences->pluck('position')->implode(' '),
        ]));
        $query = $this->normalize($position);

        if ($candidateText === '' || $query === '') {
            return false;
        }
        if (str_contains($candidateText, $query) || str_contains($query, $candidateText)) {
            return true;
        }

        $ignored = ['nhan', 'vien', 'chuyen', 'gia', 'senior', 'junior', 'staff', 'and', 'the'];
        $terms = collect(preg_split('/\s+/', $query) ?: [])
            ->filter(fn (string $term) => mb_strlen($term) >= 3 && ! in_array($term, $ignored, true));

        return $terms->isNotEmpty()
            && $terms->filter(fn (string $term) => str_contains($candidateText, $term))->count() >= max(1, (int) ceil($terms->count() * 0.35));
    }

    private function locationMatches(?string $address, string $location): bool
    {
        return $address !== null && str_contains($this->normalize($address), $this->normalize($location));
    }

    private function salaryMatches(CandidateProfile $candidate, mixed $salaryMin, mixed $salaryMax): bool
    {
        if ($salaryMin === null && $salaryMax === null) {
            return true;
        }
        if ($candidate->desired_salary_min === null && $candidate->desired_salary_max === null) {
            return true;
        }

        $targetMin = $salaryMin !== null ? (float) $salaryMin : 0;
        $targetMax = $salaryMax !== null ? (float) $salaryMax : INF;
        $candidateMin = $candidate->desired_salary_min !== null ? (float) $candidate->desired_salary_min : 0;
        $candidateMax = $candidate->desired_salary_max !== null ? (float) $candidate->desired_salary_max : INF;

        return $candidateMin <= $targetMax && $targetMin <= $candidateMax;
    }

    private function normalize(string $value): string
    {
        return trim(Str::ascii(mb_strtolower($value)));
    }

    public function show(Request $request, CandidateProfile $candidate, CandidateSearchability $searchability): View
    {
        $candidate = $this->visibleCandidate($candidate, $searchability);
        $candidate->load([
            'user',
            'skills',
            'educations' => fn ($query) => $query->orderByDesc('start_date'),
            'experiences' => fn ($query) => $query->orderByDesc('start_date'),
            'resumes' => fn ($query) => $query->where('is_default', true)->latest(),
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
        abort_unless((int) $resume->candidate_id === (int) $candidate->id && $resume->is_default, 404);
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
