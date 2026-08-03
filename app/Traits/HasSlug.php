<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Generate a unique slug from $source, appending -2, -3, ... on collision.
     * $ignoreId excludes the current record when updating.
     */
    public static function generateUniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $slug = Str::slug($source);
        $original = $slug;
        $i = 2;

        $query = fn ($s) => static::withTrashed()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $s)
            ->exists();

        while ($query($slug)) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }
}
