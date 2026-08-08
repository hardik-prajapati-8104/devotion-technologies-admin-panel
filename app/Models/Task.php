<?php

namespace App\Models;

use App\Traits\Recyclable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{ 
    use SoftDeletes, Recyclable;

    protected $fillable = [
        'board_id', 'column_id', 'title', 'description', 'priority',
        'due_date', 'position', 'completed_at', 'created_by',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
    ];

    public function recyclableRelations(): array
    {
        return ['comments', 'attachments', 'checklists'];
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(TaskBoard::class, 'board_id');
    }

    public function column(): BelongsTo
    {
        return $this->belongsTo(TaskColumn::class, 'column_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'task_assignees', 'task_id', 'admin_id')->withTimestamps();
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(TaskLabel::class, 'task_label_task', 'task_id', 'label_id');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(TaskChecklist::class, 'task_id')->orderBy('position');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class, 'task_id')->latest();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class, 'task_id')->orderBy('created_at');
    }

    public function priorityColor(): string
    {
        return match ($this->priority) {
            'urgent' => 'danger',
            'high'   => 'warning',
            'medium' => 'info',
            'low'    => 'secondary',
            default  => 'light',
        };
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && ! $this->completed_at;
    }

    public function checklistProgress(): array
    {
        $items = $this->checklists->flatMap->items;
        $total = $items->count();
        $done = $items->where('is_completed', true)->count();

        return ['done' => $done, 'total' => $total];
    }
}
