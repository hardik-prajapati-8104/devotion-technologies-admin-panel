<?php

namespace App\Models;

use App\Traits\Recyclable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskBoard extends Model
{
    use SoftDeletes, Recyclable;

    protected $fillable = ['name', 'description', 'color', 'created_by'];

    public function columns(): HasMany
    {
        return $this->hasMany(TaskColumn::class, 'board_id')->orderBy('position');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'board_id');
    }

    public function labels(): HasMany
    {
        return $this->hasMany(TaskLabel::class, 'board_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'task_board_members', 'board_id', 'admin_id')
            ->withPivot('is_owner')
            ->withTimestamps();
    }

    public function isMember(Admin $admin): bool
    {
        return $this->created_by === $admin->id || $this->members()->where('admin_id', $admin->id)->exists();
    }

    public function isOwner(Admin $admin): bool
    {
        if ($this->created_by === $admin->id) {
            return true;
        }

        return $this->members()->where('admin_id', $admin->id)->where('is_owner', true)->exists();
    }
}
