<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Announcement extends Model
{
    protected $fillable = [
        'title', 'body', 'status', 'is_pinned', 'published_at', 'expires_at', 'created_by',
    ];

    protected $casts = [
        'is_pinned'    => 'boolean',
        'published_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function reads(): MorphMany
    {
        return $this->morphMany(ReadReceipt::class, 'readable');
    }

    public function scopeVisible($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function isReadBy(Admin $admin): bool
    {
        return $this->reads()->where('admin_id', $admin->id)->exists();
    }

    public function markReadBy(Admin $admin): void
    {
        $this->reads()->firstOrCreate(['admin_id' => $admin->id]);
    }
}
