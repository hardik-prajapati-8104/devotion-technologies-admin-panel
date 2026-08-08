<?php

namespace App\Models;

use App\Traits\Recyclable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Country extends Model
{
    use HasFactory, SoftDeletes, Recyclable;

    protected $fillable = [
        'name',
        'code',
        'phone_code',
        'flag',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }
  
   
}