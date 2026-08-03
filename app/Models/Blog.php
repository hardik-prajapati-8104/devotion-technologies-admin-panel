<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use SoftDeletes, HasSlug;

    public const STATUSES = [
        'draft'     => 'Draft',
        'published' => 'Published',
        'scheduled' => 'Scheduled',
    ];

    protected $fillable = [
        'blog_category_id', 'admin_id', 'title', 'slug', 'short_description', 'content',
        'featured_image', 'thumbnail', 'reading_time', 'publish_date', 'status', 'is_featured',
        'seo_title', 'meta_description', 'focus_keyword', 'canonical_url',
        'og_title', 'og_description', 'og_image',
    ];

    protected $casts = [
        'is_featured'  => 'boolean',
        'publish_date' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function tags()
    {
        return $this->belongsToMany(BlogTag::class, 'blog_tag_map');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('publish_date')->orWhere('publish_date', '<=', now());
            });
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('publish_date')->orderByDesc('created_at');
    }

    /**
     * Estimate reading time (in minutes) from the plain-text word count.
     * Called from the controller if the admin leaves reading_time blank.
     */
    public static function estimateReadingTime(?string $html): int
    {
        $words = str_word_count(strip_tags((string) $html));
        return max(1, (int) ceil($words / 200)); // ~200 wpm
    }
}
