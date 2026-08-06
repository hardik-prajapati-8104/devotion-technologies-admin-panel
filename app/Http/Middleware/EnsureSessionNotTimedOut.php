<?php

namespace App\Http\Middleware;

use App\Models\AdminSecuritySetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSessionNotTimedOut
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::guard('admin')->check()) {
            return $next($request);
        }

        $minutes = AdminSecuritySetting::current()->session_timeout_minutes;

        if ($minutes > 0) {
            $lastActivity = $request->session()->get('admin_last_activity');

            if ($lastActivity && now()->diffInMinutes($lastActivity) >= $minutes) {
                Auth::guard('admin')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('admin.login')
                    ->with('status', 'You were logged out due to inactivity.');
            }
        }

        $request->session()->put('admin_last_activity', now());

        return $next($request);
    }
}
