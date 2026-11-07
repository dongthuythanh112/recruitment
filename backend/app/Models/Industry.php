<?php

namespace App\Models;

use App\Models\Concerns\HasUniqueSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Industry extends Model
{
    use HasUniqueSlug;

    public const SLUG_SOURCE = 'name';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_LABELS = [
        self::STATUS_ACTIVE => 'Đang dùng',
        self::STATUS_INACTIVE => 'Ngừng dùng',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'status',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    /**
     * Id của ngành này và toàn bộ ngành con cháu: không được chọn làm ngành cha,
     * nếu không cây ngành nghề sẽ bị vòng lặp.
     *
     * @return array<int, int>
     */
    public function selfAndDescendantIds(): array
    {
        $ids = [$this->id];
        $frontier = [$this->id];

        while ($frontier !== []) {
            $frontier = self::whereIn('parent_id', $frontier)->whereNotIn('id', $ids)->pluck('id')->all();
            $ids = array_merge($ids, $frontier);
        }

        return $ids;
    }
}
