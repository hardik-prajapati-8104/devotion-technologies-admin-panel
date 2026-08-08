<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskChecklistItem extends Model
{
    protected $fillable = ['checklist_id', 'title', 'is_completed', 'position'];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(TaskChecklist::class, 'checklist_id');
    }
}
