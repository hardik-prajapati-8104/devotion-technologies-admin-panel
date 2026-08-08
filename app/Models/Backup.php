<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Backup extends Model
{
    protected $fillable = [
        'type', 'disk', 'filename', 'size', 'status', 'error_message',
        'source', 'triggered_by', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'triggered_by');
    }

    public function path(): string
    {
        return "backups/{$this->filename}";
    }

    public function exists(): bool
    {
        return Storage::disk($this->disk)->exists($this->path());
    }

    public function humanSize(): ?string
    {
        if (! $this->size) {
            return null;
        }

        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $bytes < 10 ? 1 : 0) . ' ' . $units[$i];
    }
}
