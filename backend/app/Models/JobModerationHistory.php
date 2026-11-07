<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobModerationHistory extends Model
{
    public const UPDATED_AT = null;

    public const ACTION_APPROVE = 'approve';

    public const ACTION_REJECT = 'reject';

    public const ACTION_HIDE = 'hide';

    public const ACTION_RESTORE = 'restore';

    public const ACTION_LABELS = [
        self::ACTION_APPROVE => 'Duyệt',
        self::ACTION_REJECT => 'Từ chối',
        self::ACTION_HIDE => 'Ẩn tin',
        self::ACTION_RESTORE => 'Khôi phục',
    ];

    protected $fillable = [
        'job_id',
        'admin_id',
        'action',
        'note',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
