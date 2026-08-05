<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Notice extends Model
{
    protected $fillable = [
        'title', 'body', 'status', 'target_roles', 'published_at', 'expires_at', 'created_by',
    ];

    protected $casts = [
        'target_roles' => 'array',
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

    /**
     * Restrict to notices aimed at the given admin — either untargeted
     * (visible to everyone) or matching one of their roles.
     */
    public function scopeForAdmin($query, Admin $admin)
    {
        $roles = $admin->getRoleNames()->all();

        return $query->where(function ($q) use ($roles) {
            $q->whereNull('target_roles');
            foreach ($roles as $role) {
                $q->orWhereJsonContains('target_roles', $role);
            }
        });
    }

    public function audienceLabel(): string
    {
        return empty($this->target_roles) ? 'Everyone' : implode(', ', $this->target_roles);
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
