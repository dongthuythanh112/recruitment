<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(CandidateProfile::class, 'candidate_skills', 'skill_id', 'candidate_id')
            ->withPivot('level', 'created_at');
    }

    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_skills', 'skill_id', 'job_id')
            ->withPivot('is_required', 'created_at');
    }
}
