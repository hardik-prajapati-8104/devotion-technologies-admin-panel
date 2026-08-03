<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Career extends Model
{
    use SoftDeletes, HasSlug;

    public const EMPLOYMENT_TYPES = [
        'full_time'  => 'Full Time',
        'part_time'  => 'Part Time',
        'internship' => 'Internship',
        'contract'   => 'Contract',
        'remote'     => 'Remote',
    ];

    protected $fillable = [
        'title', 'slug', 'department', 'location', 'employment_type', 'experience',
        'salary_info', 'short_description', 'description', 'responsibilities',
        'requirements', 'benefits', 'application_deadline', 'status', 'is_featured',
        'seo_title', 'meta_description',
    ];

    protected $casts = [
        'status'               => 'boolean',
        'is_featured'          => 'boolean',
        'application_deadline' => 'date',
    ];

    public function applications()
    {
        return $this->hasMany(CareerApplication::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('application_deadline')->orWhere('application_deadline', '>=', now());
            });
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('is_featured')->orderByDesc('created_at');
    }

    public function getEmploymentTypeLabelAttribute(): string
    {
        return self::EMPLOYMENT_TYPES[$this->employment_type] ?? ucfirst($this->employment_type);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->application_deadline && $this->application_deadline->isPast();
    }
}
