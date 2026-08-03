<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactEnquiry extends Model
{
    use SoftDeletes;

    public const STATUSES = [
        'new'         => 'New',
        'read'        => 'Read',
        'in_progress' => 'In Progress',
        'replied'     => 'Replied',
        'closed'      => 'Closed',
    ];

    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message', 'source', 'status', 'ip_address',
    ];

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}
