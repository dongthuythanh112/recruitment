<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Experience extends Model
{
    protected $table = 'experiences';

    protected $fillable = [
        'candidate_id',
        'company_name',
        'position',
        'start_date',
        'end_date',
        'is_current',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }
}
