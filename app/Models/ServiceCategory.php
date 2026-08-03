<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCategory extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = [
        'name', 'slug', 'description', 'status', 'seo_title', 'meta_description',
    ];

    protected $casts = ['status' => 'boolean'];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
