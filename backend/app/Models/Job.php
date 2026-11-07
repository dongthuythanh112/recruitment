<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Tin tuyển dụng (bảng `jobs`). Bảng queue của Laravel đã đổi thành `queue_jobs`.
 */
class Job extends Model
{
    use HasUniqueSlug;

    public const SLUG_SOURCE = 'title';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_HIDDEN = 'hidden';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_LABELS = [
        self::STATUS_DRAFT => 'Nháp',
        self::STATUS_PENDING => 'Chờ duyệt',
        self::STATUS_APPROVED => 'Đã duyệt',
        self::STATUS_REJECTED => 'Bị từ chối',
        self::STATUS_HIDDEN => 'Đã ẩn',
        self::STATUS_CLOSED => 'Đã đóng',
    ];

    public const EMPLOYMENT_TYPE_LABELS = [
        'full_time' => 'Toàn thời gian',
        'part_time' => 'Bán thời gian',
        'contract' => 'Hợp đồng',
        'internship' => 'Thực tập',
    ];

    public const WORKPLACE_TYPE_LABELS = [
        'onsite' => 'Tại văn phòng',
        'remote' => 'Từ xa',
        'hybrid' => 'Kết hợp',
    ];

    /**
     * Hành động kiểm duyệt: từ trạng thái nào chuyển sang trạng thái nào.
     */
    public const MODERATION_ACTIONS = [
        JobModerationHistory::ACTION_APPROVE => ['from' => self::STATUS_PENDING, 'to' => self::STATUS_APPROVED],
        JobModerationHistory::ACTION_REJECT => ['from' => self::STATUS_PENDING, 'to' => self::STATUS_REJECTED],
        JobModerationHistory::ACTION_HIDE => ['from' => self::STATUS_APPROVED, 'to' => self::STATUS_HIDDEN],
        JobModerationHistory::ACTION_RESTORE => ['from' => self::STATUS_HIDDEN, 'to' => self::STATUS_APPROVED],
    ];

    protected $fillable = [
        'company_id',
        'created_by',
        'industry_id',
        'title',
        'slug',
        'description',
        'requirements',
        'benefits',
        'employment_type',
        'workplace_type',
        'location',
        'salary_min',
        'salary_max',
        'salary_currency',
        'experience_years',
        'quantity',
        'deadline',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'experience_years' => 'decimal:1',
            'deadline' => 'date',
            'reviewed_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function salaryRange(): string
    {
        $format = fn ($amount) => number_format((float) $amount, 0, ',', '.');
        $currency = $this->salary_currency ?? 'VND';

        return match (true) {
            $this->salary_min !== null && $this->salary_max !== null => $format($this->salary_min).' - '.$format($this->salary_max).' '.$currency,
            $this->salary_min !== null => 'Từ '.$format($this->salary_min).' '.$currency,
            $this->salary_max !== null => 'Đến '.$format($this->salary_max).' '.$currency,
            default => 'Thỏa thuận',
        };
    }

    /**
     * Thực hiện một hành động kiểm duyệt và ghi lịch sử trong cùng một transaction.
     * Khóa dòng để hai admin không thể xử lý chồng lên cùng một tin.
     *
     * @throws ValidationException khi trạng thái hiện tại không cho phép hành động này
     */
    public function moderate(string $action, User $admin, ?string $note = null): void
    {
        $transition = self::MODERATION_ACTIONS[$action];

        DB::transaction(function () use ($action, $admin, $note, $transition) {
            $current = self::whereKey($this->id)->lockForUpdate()->firstOrFail();

            if ($current->status !== $transition['from']) {
                throw ValidationException::withMessages([
                    'action' => 'Tin đang ở trạng thái "'.self::STATUS_LABELS[$current->status].'" nên không thể thực hiện thao tác này.',
                ]);
            }

            $current->update([
                'status' => $transition['to'],
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'rejection_reason' => $action === JobModerationHistory::ACTION_REJECT ? $note : null,
                'published_at' => $transition['to'] === self::STATUS_APPROVED
                    ? ($current->published_at ?? now())
                    : $current->published_at,
            ]);

            $current->moderationHistories()->create([
                'admin_id' => $admin->id,
                'action' => $action,
                'note' => $note,
            ]);
        });

        $this->refresh();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_skills', 'job_id', 'skill_id')
            ->withPivot('is_required', 'created_at');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function moderationHistories(): HasMany
    {
        return $this->hasMany(JobModerationHistory::class);
    }
}
