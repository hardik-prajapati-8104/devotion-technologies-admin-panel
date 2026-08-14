<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Get a single setting value.
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::allSettings();

        return $settings[$key] ?? $default;
    }

    /**
     * Set a setting value.
     */
    public static function set(
        string $key,
        $value,
        string $group = 'general'
    ): void {
        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
            ]
        );

        Cache::forget('settings.all');
    }

    /**
     * Get settings by group.
     */
    public static function group(string $group): array
    {
        return self::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Get all settings.
     */
    public static function allSettings(): array
    {
        return Cache::rememberForever('settings.all', function () {
            return self::pluck('value', 'key')->toArray();
        });
    }
}