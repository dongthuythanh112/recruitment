<?php

namespace App\Services;

use App\Models\CandidateProfile;
use App\Models\Job;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class JobRecommendationService
{
    /**
     * @return array{matched: Collection<int, array{job: Job, reasons: array<int, string>}>, featured: Collection<int, Job>}
     */
    public function recommend(?CandidateProfile $candidate, int $limit = 6): array
    {
        $jobs = Job::published()
            ->with(['company', 'industry', 'skills'])
            ->withCount('applications')
            ->get();

        $matched = collect();

        if ($candidate) {
            $candidate->loadMissing('skills');
            $ranked = $jobs->map(function (Job $job) use ($candidate): array {
                $reasons = [];
                $position = $this->normalize($candidate->desired_position);
                $jobText = $this->normalize($job->title.' '.$job->industry?->name);

                if ($position !== '' && $this->matchesPosition($position, $jobText)) {
                    $reasons[] = 'Ngành nghề/vị trí phù hợp';
                }

                if ($this->matchesLocation($candidate->address, $job->location)) {
                    $reasons[] = 'Địa điểm phù hợp';
                }

                if ($this->matchesSalary($candidate, $job)) {
                    $reasons[] = 'Mức lương phù hợp';
                }

                if ($this->matchesExperience($candidate, $job)) {
                    $reasons[] = 'Kinh nghiệm phù hợp';
                }

                $candidateSkills = $candidate->skills
                    ->map(fn ($skill) => $this->normalize($skill->name))
                    ->filter()
                    ->all();
                $sharedSkills = $job->skills
                    ->filter(fn ($skill) => in_array($this->normalize($skill->name), $candidateSkills, true));

                if ($sharedSkills->isNotEmpty()) {
                    $reasons[] = 'Khớp kỹ năng: '.$sharedSkills->take(2)->pluck('name')->implode(', ');
                }

                return ['job' => $job, 'reasons' => $reasons, 'score' => count($reasons)];
            })
                ->filter(fn (array $item) => $item['score'] > 0)
                ->sort(function (array $a, array $b): int {
                    return ($b['score'] <=> $a['score'])
                        ?: (($b['job']->published_at?->timestamp ?? 0) <=> ($a['job']->published_at?->timestamp ?? 0));
                })
                ->take($limit)
                ->values();

            $matched = $ranked->map(fn (array $item) => [
                'job' => $item['job'],
                'reasons' => $item['reasons'],
            ]);
        }

        $matchedIds = $matched->pluck('job.id')->all();
        $featured = $jobs
            ->reject(fn (Job $job) => in_array($job->id, $matchedIds, true))
            ->sort(function (Job $a, Job $b): int {
                return (($b->applications_count ?? 0) <=> ($a->applications_count ?? 0))
                    ?: (($b->published_at?->timestamp ?? 0) <=> ($a->published_at?->timestamp ?? 0));
            })
            ->take($limit)
            ->values();

        return compact('matched', 'featured');
    }

    private function matchesPosition(string $position, string $jobText): bool
    {
        if (str_contains($jobText, $position) || str_contains($position, $jobText)) {
            return true;
        }

        $ignored = ['nhan', 'vien', 'chuyen', 'gia', 'cao', 'cap', 'junior', 'senior', 'staff'];
        $terms = collect(preg_split('/\s+/', $position) ?: [])
            ->filter(fn (string $term) => mb_strlen($term) >= 3 && ! in_array($term, $ignored, true));

        return $terms->isNotEmpty() && $terms->contains(fn (string $term) => str_contains($jobText, $term));
    }

    private function matchesLocation(?string $address, ?string $location): bool
    {
        if (! $address || ! $location) {
            return false;
        }

        $jobLocation = $this->normalize($location);
        $addressParts = collect(preg_split('/[,;]+/', $address) ?: [])
            ->map(fn (string $part) => $this->normalize($part))
            ->filter();

        return $addressParts->contains(fn (string $part) => mb_strlen($part) >= 3
            && (str_contains($jobLocation, $part) || str_contains($part, $jobLocation)));
    }

    private function matchesSalary(CandidateProfile $candidate, Job $job): bool
    {
        if ($candidate->desired_salary_min === null && $candidate->desired_salary_max === null) {
            return false;
        }

        if ($job->salary_min === null && $job->salary_max === null) {
            return false;
        }

        $candidateMin = $candidate->desired_salary_min !== null ? (float) $candidate->desired_salary_min : 0;
        $candidateMax = $candidate->desired_salary_max !== null ? (float) $candidate->desired_salary_max : INF;
        $jobMin = $job->salary_min !== null ? (float) $job->salary_min : 0;
        $jobMax = $job->salary_max !== null ? (float) $job->salary_max : INF;

        return $jobMin <= $candidateMax && $candidateMin <= $jobMax;
    }

    private function matchesExperience(CandidateProfile $candidate, Job $job): bool
    {
        return $candidate->years_of_experience !== null
            && $job->experience_years !== null
            && (float) $candidate->years_of_experience >= (float) $job->experience_years;
    }

    private function normalize(?string $value): string
    {
        return trim(Str::ascii(mb_strtolower($value ?? '')));
    }
}
