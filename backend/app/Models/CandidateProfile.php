<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandidateProfile extends Model
{
    public const GENDER_LABELS = [
        'male' => 'Nam',
        'female' => 'Nữ',
        'other' => 'Khác',
    ];

    protected $fillable = [
        'user_id',
        'avatar',
        'date_of_birth',
        'gender',
        'address',
        'summary',
        'desired_position',
        'desired_salary_min',
        'desired_salary_max',
        'years_of_experience',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'desired_salary_min' => 'decimal:2',
            'desired_salary_max' => 'decimal:2',
            'years_of_experience' => 'decimal:1',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resumes(): HasMany
    {
        return $this->hasMany(Resume::class, 'candidate_id');
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class, 'candidate_id');
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class, 'candidate_id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'candidate_skills', 'candidate_id', 'skill_id')
            ->withPivot('level', 'created_at');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'candidate_id');
    }

    public function savedJobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'saved_jobs', 'candidate_id', 'job_id')
            ->withPivot('created_at');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'candidate_id');
    }
}
