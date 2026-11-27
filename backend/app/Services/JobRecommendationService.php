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
            ->with(['company', 'industry'])
            ->withCount('applications')
            ->get();

        $matched = collect();

        if ($candidate) {
            $ranked = $jobs->map(function (Job $job) use ($candidate): array {
                $position = $this->normalize($candidate->desired_position);
                $jobText = $this->normalize($job->title.' '.$job->industry?->name);
                $matches = $position !== '' && $this->matchesPosition($position, $jobText);

                return [
                    'job' => $job,
                    'reasons' => $matches ? ['Vị trí mong muốn phù hợp với tiêu đề tin và ngành nghề'] : [],
                    'score' => (int) $matches,
                ];
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

    private function normalize(?string $value): string
    {
        return trim(Str::ascii(mb_strtolower($value ?? '')));
    }
}
