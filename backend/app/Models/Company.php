<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasUniqueSlug;

    public const SLUG_SOURCE = 'name';

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'website',
        'email',
        'phone',
        'address',
        'company_size',
        'tax_code',
        'status',
        'created_by',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recruiters(): HasMany
    {
        return $this->hasMany(RecruiterProfile::class);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }
}
