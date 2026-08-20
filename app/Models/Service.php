<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Service extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = [
        'service_category_id', 'name', 'slug', 'short_description', 'full_description',
        'featured_image', 'icon', 'display_order', 'is_featured', 'status',
        'seo_title', 'meta_description', 'og_image','brochure'
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

        public function getHasBrochureAttribute(): bool
    {
        return $this->brochure && Storage::disk('public')->exists($this->brochure);
    }
}
