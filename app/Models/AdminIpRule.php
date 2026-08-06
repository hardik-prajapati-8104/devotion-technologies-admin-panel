<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminIpRule extends Model
{
    protected $fillable = ['ip', 'type', 'note', 'created_by'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function scopeWhitelist($query)
    {
        return $query->where('type', 'whitelist');
    }

    public function scopeBlacklist($query)
    {
        return $query->where('type', 'blacklist');
    }

    /**
     * True if $ip matches this rule's ip/CIDR. Supports plain IPv4/IPv6
     * addresses and IPv4 CIDR ranges (e.g. 203.0.113.0/24).
     */
    public function matches(string $ip): bool
    {
        if (! str_contains($this->ip, '/')) {
            return $this->ip === $ip;
        }

        [$subnet, $bits] = explode('/', $this->ip, 2);

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false; // not IPv4 — CIDR matching not attempted for IPv6 here
        }

        $mask = -1 << (32 - (int) $bits);

        return ($ipLong & $mask) === ($subnetLong & $mask);
    }

    public static function isBlacklisted(string $ip): bool
    {
        return static::blacklist()->get()->contains(fn ($rule) => $rule->matches($ip));
    }

    /**
     * Whitelisting is "allow-list mode": if ANY whitelist rows exist, only
     * matching IPs pass. If none exist, whitelist is treated as inactive
     * (so setting up IP rules can never accidentally lock every admin out
     * before a rule has been added).
     */
    public static function isWhitelisted(string $ip): bool
    {
        $rules = static::whitelist()->get();

        if ($rules->isEmpty()) {
            return true;
        }

        return $rules->contains(fn ($rule) => $rule->matches($ip));
    }
}
