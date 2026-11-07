<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resume extends Model
{
    protected $fillable = [
        'candidate_id',
        'title',
        'file_path',
        'original_filename',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
