<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RecycleBinMeta extends Model
{
    protected $table = 'recycle_bin_meta';

    protected $fillable = [
        'recyclable_type', 'recyclable_id', 'status',
        'deleted_by', 'deleted_at', 'archived_by', 'archived_at', 'auto_delete_at',
    ];

    protected $casts = [
        'deleted_at'     => 'datetime',
        'archived_at'    => 'datetime',
        'auto_delete_at' => 'datetime',
    ];

    public function recyclable(): MorphTo
    {
        return $this->morphTo();
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'deleted_by');
    }

    public function archivedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'archived_by');
    }

    public function scopeTrashed($query)
    {
        return $query->where('status', 'trashed');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function daysUntilAutoDelete(): ?int
    {
        return $this->auto_delete_at ? max(0, now()->diffInDays($this->auto_delete_at, false)) : null;
    }
}
