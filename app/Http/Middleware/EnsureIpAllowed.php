<?php

namespace App\Http\Middleware;

use App\Models\AdminIpRule;
use App\Models\AdminSecuritySetting;
use Closure;
use Illuminate\Http\Request;

class EnsureIpAllowed
{
    public function handle(Request $request, Closure $next)
    {
        $settings = AdminSecuritySetting::current();

        if (! $settings->ip_rules_enabled) {
            return $next($request);
        }

        $ip = $request->ip();

        if (AdminIpRule::isBlacklisted($ip)) {
            abort(403, 'Your IP address has been blocked from accessing this panel.');
        }

        if (! AdminIpRule::isWhitelisted($ip)) {
            abort(403, 'Your IP address is not authorized to access this panel.');
        }

        return $next($request);
    }
}
