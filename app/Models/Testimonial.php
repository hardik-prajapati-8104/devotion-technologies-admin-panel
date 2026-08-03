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
}
