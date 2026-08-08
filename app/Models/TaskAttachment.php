<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

class TaskAttachment extends Model
{
    protected $fillable = ['task_id', 'admin_id', 'original_name', 'file_path', 'mime_type', 'size'];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function getHumanSizeAttribute(): string
    {
        return Number::fileSize($this->size);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }
}
