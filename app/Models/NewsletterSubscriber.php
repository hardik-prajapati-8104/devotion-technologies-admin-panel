<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'name', 'status', 'subscribed_at'];

    protected $casts = [
        'status'        => 'boolean',
        'subscribed_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
