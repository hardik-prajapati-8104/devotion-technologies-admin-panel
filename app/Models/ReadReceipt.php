<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadReceipt extends Model
{
    protected $fillable = ['readable_type', 'readable_id', 'admin_id', 'read_at'];

    public $timestamps = false;

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
