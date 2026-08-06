<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLoginAttempt extends Model
{
    public $timestamps = false; // only created_at is used, set explicitly below

    protected $fillable = ['email', 'ip', 'user_agent', 'successful', 'reason', 'created_at'];

    protected $casts = [
        'successful' => 'boolean',
        'created_at' => 'datetime',
    ];

    public static function log(string $email, string $ip, ?string $userAgent, bool $successful, ?string $reason = null): self
    {
        return static::create([
            'email'       => $email,
            'ip'          => $ip,
            'user_agent'  => $userAgent,
            'successful'  => $successful,
            'reason'      => $reason,
            'created_at'  => now(),
        ]);
    }

    /**
     * Count of recent failed attempts for $email within the last
     * $minutes — used to decide whether to lock the account, independent
     * of (and in addition to) Laravel's RateLimiter which is IP+email
     * keyed but cache-backed and doesn't survive a cache flush.
     */
    public static function recentFailedCount(string $email, int $minutes): int
    {
        return static::where('email', $email)
            ->where('successful', false)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }
}
