<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CandidateSearchability
{
    private const IDS_KEY = 'candidate_searchability.profile_ids';

    public function isEnabled(int $candidateId): bool
    {
        return in_array($candidateId, $this->candidateIds(), true);
    }

    /** @return array<int, int> */
    public function candidateIds(): array
    {
        return collect(Cache::store('database')->get(self::IDS_KEY, []))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    public function setEnabled(int $candidateId, bool $enabled): void
    {
        Cache::store('database')->lock(self::IDS_KEY.'.lock', 10)->block(5, function () use ($candidateId, $enabled): void {
            $ids = $this->candidateIds();
            $ids = $enabled
                ? array_values(array_unique([...$ids, $candidateId]))
                : array_values(array_filter($ids, fn (int $id) => $id !== $candidateId));

            Cache::store('database')->forever(self::IDS_KEY, $ids);
        });
    }
}
