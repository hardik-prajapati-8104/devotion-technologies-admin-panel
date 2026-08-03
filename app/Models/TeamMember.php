<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamMember extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'designation', 'department', 'profile_image', 'biography',
        'email', 'phone', 'linkedin_url', 'facebook_url', 'instagram_url', 'twitter_url',
        'display_order', 'status',
    ];

    protected $casts = ['status' => 'boolean'];

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    /**
     * Social links as a clean [platform => url] array, skipping empties —
     * convenient for the public site and for the admin edit view.
     */
    public function getSocialLinksAttribute(): array
    {
        return array_filter([
            'linkedin'  => $this->linkedin_url,
            'facebook'  => $this->facebook_url,
            'instagram' => $this->instagram_url,
            'twitter'   => $this->twitter_url,
        ]);
    }
}
