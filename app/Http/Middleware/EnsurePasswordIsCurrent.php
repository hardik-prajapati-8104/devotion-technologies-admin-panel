<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePasswordIsCurrent
{
    /**
     * Routes that must stay reachable even while a password change is
     * required, or the admin could never actually complete the change
     * (or log out).
     */
    private array $except = [
        'admin.password.change',
        'admin.password.update',
        'admin.logout',
    ];

    public function handle(Request $request, Closure $next)
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            return $next($request);
        }

        if ($request->route() && in_array($request->route()->getName(), $this->except, true)) {
            return $next($request);
        }

        if ($admin->must_change_password || $admin->isPasswordExpired()) {
            return redirect()->route('admin.password.change');
        }

        return $next($request);
    }
}
