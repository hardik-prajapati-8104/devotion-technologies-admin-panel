<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecycleBinSetting extends Model
{
    protected $fillable = ['default_retention_days', 'retention_overrides', 'updated_by'];

    protected $casts = [
        'retention_overrides' => 'array',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }

    public static function retentionDaysFor(string $modelClass): int
    {
        $settings = static::current();

        return $settings->retention_overrides[$modelClass] ?? $settings->default_retention_days;
    }
}
