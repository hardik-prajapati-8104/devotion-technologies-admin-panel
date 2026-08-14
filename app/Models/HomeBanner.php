<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class HomeBanner extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'image', 'button_text', 'button_link',
        'sort_order', 'status', 'starts_at', 'ends_at', 'created_by',
    ];

    protected $casts = [
        'status'     => 'boolean',
        'starts_at'  => 'datetime',
        'ends_at'    => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    /**
     * status=true AND (no schedule, or currently within it) — this is
     * what the public homepage should actually query, as opposed to
     * the admin list which shows everything regardless of schedule.
     */
    public function scopeLive($query)
    {
        return $query->where('status', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->orderBy('sort_order');
    }

    public function isScheduled(): bool
    {
        return $this->starts_at !== null || $this->ends_at !== null;
    }

    public function isCurrentlyLive(): bool
    {
        if (! $this->status) {
            return false;
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->ends_at && $this->ends_at->isPast()) {
            return false;
        }
        return true;
    }
}
