<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecruiterProfile extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'position',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Tin tuyển dụng do nhà tuyển dụng này đăng (jobs.created_by trỏ tới users.id).
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'created_by', 'user_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'recruiter_id');
    }
}
