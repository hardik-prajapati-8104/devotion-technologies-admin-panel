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
     * Set a setting value. Creates the row if it doesn't exist yet,
     * otherwise updates it in place (matched on `key`).
     */
    public static function set(string $key, $value, string $group = 'general'): void
    {
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
     * Delete a setting by key. Used by the dynamic Custom Settings CRUD
     * (also usable directly, e.g. in tinker or a seeder/cleanup script).
     */
    public static function remove(string $key): void
    {
        self::where('key', $key)->delete();

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
     * Get all settings, cached forever (invalidated on every write/delete).
     */
    public static function allSettings(): array
    {
        return Cache::rememberForever('settings.all', function () {
            return self::pluck('value', 'key')->toArray();
        });
    }
}
