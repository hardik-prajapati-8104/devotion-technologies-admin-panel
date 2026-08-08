<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupSetting extends Model
{
    protected $fillable = [
        'schedule_enabled', 'frequency', 'time', 'retention_days',
        'backup_database', 'backup_files', 'file_paths', 'notify_email',
        'last_run_at',
    ];

    protected $casts = [
        'schedule_enabled' => 'boolean',
        'backup_database'  => 'boolean',
        'backup_files'     => 'boolean',
        'last_run_at'      => 'datetime',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1], [
            'file_paths' => "storage/app/public",
        ]);
    }

    /**
     * Configured backup source directories, relative to base_path(),
     * one per line in the settings textarea. Blank lines and lines that
     * don't resolve to an existing directory are silently dropped —
     * safer than failing the whole backup over one stale path.
     */
    public function filePathList(): array
    {
        return collect(preg_split('/\r?\n/', (string) $this->file_paths))
            ->map(fn ($p) => trim($p))
            ->filter(fn ($p) => $p !== '' && is_dir(base_path($p)))
            ->values()
            ->all();
    }
}
