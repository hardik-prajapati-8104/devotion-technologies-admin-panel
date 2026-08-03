<?php

namespace App\Models;

use App\Services\AdminMenuBadgeResolver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class AdminMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'type',
        'title',
        'icon',
        'route_name',
        'route_pattern',
        'permission',
        'badge_key',
        'open_in_new_tab',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'open_in_new_tab' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    const CACHE_KEY = 'admin_menu_tree';

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function allChildren(): HasMany
    {
        // Unfiltered — used by the admin management screens
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Resolved URL for this menu item. Falls back to "#" for group-only parents.
     */
    public function getUrlAttribute(): string
    {
        if (! $this->route_name) {
            return '#';
        }

        return \Illuminate\Support\Facades\Route::has($this->route_name)
            ? route($this->route_name)
            : '#';
    }

    /**
     * True if the current request matches this item's route pattern(s),
     * used for the "active" class on links and "open" class on parent <li>s.
     */
    public function getIsActiveRouteAttribute(): bool
    {
        $patterns = collect(explode(',', (string) $this->route_pattern))
            ->map(fn ($p) => trim($p))
            ->filter();

        if ($patterns->isEmpty() && $this->route_name) {
            $patterns = collect([$this->route_name . '*']);
        }

        return $patterns->contains(fn ($pattern) => request()->routeIs($pattern));
    }

    /**
     * Dynamic count badge (e.g. "3 new applications"), null if none configured.
     */
    public function getBadgeCountAttribute(): ?int
    {
        if (! $this->badge_key) {
            return null;
        }

        return AdminMenuBadgeResolver::resolve($this->badge_key);
    }

    /**
     * True if the logged-in admin has permission to see this item.
     * Items without a permission set are always visible.
     */
    public function getIsVisibleAttribute(): bool
    {
        if (! $this->permission) {
            return true;
        }

        $user = auth('admin')->user() ?? auth()->user();

        return $user && method_exists($user, 'can') && $user->can($this->permission);
    }

    /**
     * Full nested, permission-filtered menu tree.
     */
    public static function tree()
    {
        $items = self::active()
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (AdminMenu $item) => $item->is_visible)
            ->values();

        $grouped = $items->groupBy('parent_id');

        $build = function ($parentId, array $branch = []) use (&$build, $grouped) {
            if (isset($branch[$parentId])) {
                return collect();
            }

            $branch[$parentId] = true;

            return ($grouped->get($parentId) ?? collect())
                ->map(function (AdminMenu $item) use (&$build, $branch) {
                    $item->setRelation('children', $build($item->id, $branch));

                    return $item;
                })
                ->values();
        };

        return $build(null);
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted()
    {
        static::saved(fn () => self::flushCache());
        static::deleted(fn () => self::flushCache());
    }
}
