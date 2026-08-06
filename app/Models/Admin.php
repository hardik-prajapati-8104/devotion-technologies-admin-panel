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
        'two_factor_secret', 
        'two_factor_recovery_codes', 
        'two_factor_confirmed_at',
        'must_change_password', 
        'password_changed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret', 
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'two_factor_secret'          => 'encrypted',
        'two_factor_recovery_codes'  => 'encrypted:array',
        'two_factor_confirmed_at'    => 'datetime',
        'must_change_password'       => 'boolean',
        'password_changed_at'        => 'datetime',
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

    public function hasTwoFactorEnabled(): bool
    {
        return ! is_null($this->two_factor_confirmed_at);
    }
    
    /**
     * Consumes one recovery code if valid (single-use — removes it from the
     * stored list so it can't be replayed). Returns true if it matched.
     */
    public function useRecoveryCode(string $code): bool
    {
        $code = strtoupper(trim($code));
        $codes = $this->two_factor_recovery_codes ?? [];
    
        if (! in_array($code, $codes, true)) {
            return false;
        }
    
        $this->two_factor_recovery_codes = array_values(array_diff($codes, [$code]));
        $this->save();
    
        return true;
    }
    
    public function isPasswordExpired(): bool
    {
        $days = \App\Models\AdminSecuritySetting::current()->password_expiry_days;
    
        if ($days <= 0 || ! $this->password_changed_at) {
            return false;
        }
    
        return $this->password_changed_at->addDays($days)->isPast();
    }
    
 
}
