<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'admin_id', 'original_name', 'file_path', 'mime_type', 'size', 'alt_text', 'folder',
    ];

    public function uploadedBy()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function getUrlAttribute(): string
    {
        return asset('public/storage/'.$this->file_path);
    }

    public function getIsImageAttribute(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size;

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1).' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }
}
