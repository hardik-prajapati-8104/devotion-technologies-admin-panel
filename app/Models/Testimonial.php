<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_name', 'company', 'designation', 'profile_image', 'rating',
        'testimonial', 'display_order', 'is_featured', 'status',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'status'      => 'boolean',
        'rating'      => 'integer',
    ];

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('client_name');
    }

        // Handy accessor for initials avatar fallback
    public function getInitialsAttribute()
    {
        $parts = explode(' ', trim($this->client_name));
        $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
        return $initials;
    }

     public function scopeActive($query)
    {
        return $query->where('status', 1); // adjust to your status value (e.g. 'active')
    }
}
