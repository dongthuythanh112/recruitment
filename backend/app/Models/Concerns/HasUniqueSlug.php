<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Tự sinh slug duy nhất khi tạo bản ghi nếu chưa có.
 * Model dùng trait này cần có hằng SLUG_SOURCE là tên cột dùng để tạo slug.
 */
trait HasUniqueSlug
{
    protected static function bootHasUniqueSlug(): void
    {
        static::creating(function (self $model) {
            if (blank($model->slug)) {
                $model->slug = static::uniqueSlug((string) $model->{static::SLUG_SOURCE});
            }
        });
    }

    public static function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: 'item';
        $slug = $base;
        $suffix = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
