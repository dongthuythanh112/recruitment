<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    // Laravel coi "education" là danh từ không đếm được nên phải khai báo tên bảng.
    protected $table = 'educations';

    protected $fillable = [
        'candidate_id',
        'school_name',
        'degree',
        'major',
        'start_date',
        'end_date',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }
}
