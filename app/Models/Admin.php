<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * Spatie permission models are keyed to this guard.
     */
    protected string $guard_name = 'admin';

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'mobile_number',
        'profile_image',
        'status',
        'login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'boolean',
        'login' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Accessor: full name.
     */
    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
    

    /**
     * Activity logs raised by this admin.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Only admins allowed to log in and marked active.
     */
    public function scopeLoginEnabled($query)
    {
        return $query->where('status', 1)->where('login', 1);
    }
 
    public function chatConversations()
    {
        return $this->belongsToMany(
            \App\Models\ChatConversation::class,
            'chat_conversation_participants',
            'admin_id',        // this model's (Admin) key on the pivot table
            'conversation_id'  // related model's (ChatConversation) key on the pivot table
        )
            ->withPivot('last_read_message_id', 'joined_at')
            ->withTimestamps();
    }
 
}
