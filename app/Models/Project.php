<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes, HasSlug;

    public const STATUSES = [
        'draft'       => 'Draft',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
        'archived'    => 'Archived',
    ];

    protected $fillable = [
        'project_category_id', 'name', 'slug', 'client_name', 'location',
        'short_description', 'full_description', 'featured_image', 'project_url',
        'technologies', 'completion_date', 'status', 'is_featured', 'display_order',
        'seo_title', 'meta_description', 'og_image',
    ];

    protected $casts = [
        'is_featured'      => 'boolean',
        'completion_date'  => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class)->orderBy('display_order');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    /**
     * Technologies stored as a comma-separated string; exposed as an array.
     */
    public function getTechnologiesArrayAttribute(): array
    {
        return $this->technologies
            ? array_map('trim', explode(',', $this->technologies))
            : [];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
