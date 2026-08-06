<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * The admin is already Auth::guard('admin')-authenticated at this point
 * (LoginController::login() completed a normal password check), but a
 * session flag `admin_needs_2fa` is set for anyone with 2FA enabled.
 * This middleware confines them to the 2FA challenge (and logout) route
 * until that flag is cleared by TwoFactorController::verify().
 */
class EnsureTwoFactorVerified
{
    private array $except = [
        'admin.2fa.challenge',
        'admin.2fa.verify',
        'admin.logout',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (! Auth::guard('admin')->check()) {
            return $next($request);
        }

        if (! $request->session()->get('admin_needs_2fa')) {
            return $next($request);
        }

        if ($request->route() && in_array($request->route()->getName(), $this->except, true)) {
            return $next($request);
        }

        return redirect()->route('admin.2fa.challenge');
    }
}
