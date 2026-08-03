<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model
{
    use HasSlug;

    protected $fillable = ['name', 'slug'];

    public $timestamps = true;

    public function blogs()
    {
        return $this->belongsToMany(Blog::class, 'blog_tag_map');
    }

    /**
     * Find existing tags or create new ones from a flat array of tag names.
     * Used by BlogController when saving the tag input (comma-separated / Select2 "tags" mode).
     */
    public static function resolveFromNames(array $names): array
    {
        $ids = [];

        foreach (array_filter(array_map('trim', $names)) as $name) {
            $tag = self::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($name)],
                ['name' => $name]
            );
            $ids[] = $tag->id;
        }

        return $ids;
    }
}
