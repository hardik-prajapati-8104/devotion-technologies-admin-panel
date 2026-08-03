<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectCategory extends Model
{
    use SoftDeletes, HasSlug;

    protected $fillable = ['name', 'slug', 'description', 'status'];

    protected $casts = ['status' => 'boolean'];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
