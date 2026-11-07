<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_CANDIDATE = 'candidate';

    public const ROLE_RECRUITER = 'recruiter';

    public const ROLE_ADMIN = 'admin';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_LOCKED = 'locked';

    public const STATUS_LABELS = [
        self::STATUS_ACTIVE => 'Hoạt động',
        self::STATUS_LOCKED => 'Đã khóa',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isActiveAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN && $this->status === self::STATUS_ACTIVE;
    }

    public function candidateProfile(): HasOne
    {
        return $this->hasOne(CandidateProfile::class);
    }

    public function recruiterProfile(): HasOne
    {
        return $this->hasOne(RecruiterProfile::class);
    }

    public function createdJobs(): HasMany
    {
        return $this->hasMany(Job::class, 'created_by');
    }

    /**
     * Thông báo theo bảng `notifications` của thiết kế. Không đặt tên notifications()
     * vì trùng với quan hệ của trait Notifiable (bảng thiết kế không có cột morph).
     */
    public function userNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
