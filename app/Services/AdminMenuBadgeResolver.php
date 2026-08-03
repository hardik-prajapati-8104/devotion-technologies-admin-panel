<?php

namespace App\Services;

use App\Models\CareerApplication;
use App\Models\ContactEnquiry;
use Illuminate\Support\Facades\Cache;

class AdminMenuBadgeResolver
{
    /**
     * Register new badge keys here as new modules need a count bubble.
     * Each resolver is cached for 60s, matching the original sidebar behaviour.
     */
    protected static function resolvers(): array
    {
        return [
            'new_applications' => fn () => Cache::remember(
                'sidebar_new_applications',
                60,
                fn () => CareerApplication::where('status', 'new')->count()
            ),
            'new_enquiries' => fn () => Cache::remember(
                'sidebar_new_enquiries',
                60,
                fn () => ContactEnquiry::where('status', 'new')->count()
            ),
        ];
    }

    public static function resolve(string $key): ?int
    {
        $resolver = static::resolvers()[$key] ?? null;

        return $resolver ? (int) $resolver() : null;
    }

    /**
     * Used by the menu management UI to populate a "badge key" dropdown.
     */
    public static function availableKeys(): array
    {
        return array_keys(static::resolvers());
    }
}
