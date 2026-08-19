<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingEnquiry extends Model
{
    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'service_category_id',
        'address',
        'description',
        'status',
        'email_sent',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'email_sent' => 'boolean',
    ];

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }
}
