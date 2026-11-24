<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    // Bảng applications dùng applied_at thay cho created_at.
    public const CREATED_AT = 'applied_at';

    public const STATUS_LABELS = [
        'pending' => 'Chờ xử lý',
        'reviewing' => 'Đang xem xét',
        'shortlisted' => 'Vào danh sách ngắn',
        'interview' => 'Phỏng vấn',
        'offered' => 'Đã đề nghị',
        'hired' => 'Đã tuyển',
        'rejected' => 'Từ chối',
        'withdrawn' => 'Đã rút',
    ];

    /** Các trạng thái nhà tuyển dụng được phép chọn khi xử lý hồ sơ. */
    public const RECRUITER_STATUS_LABELS = [
        'pending' => 'Chờ xử lý',
        'reviewing' => 'Đang xem xét',
        'shortlisted' => 'Vào danh sách ngắn',
        'interview' => 'Phỏng vấn',
        'offered' => 'Đã đề nghị',
        'hired' => 'Đã tuyển',
        'rejected' => 'Từ chối',
    ];

    protected $fillable = [
        'job_id',
        'candidate_id',
        'resume_id',
        'cover_letter',
        'status',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ApplicationStatusHistory::class);
    }
}
