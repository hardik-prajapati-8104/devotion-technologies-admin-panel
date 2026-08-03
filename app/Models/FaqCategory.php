<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaqCategory extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = ['name', 'slug', 'display_order', 'status'];

    protected $casts = ['status' => 'boolean'];

    public function faqs()
    {
        return $this->hasMany(Faq::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }
}
