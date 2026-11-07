<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'candidate_id',
        'recruiter_id',
        'job_id',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }

    public function recruiter(): BelongsTo
    {
        return $this->belongsTo(RecruiterProfile::class, 'recruiter_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
