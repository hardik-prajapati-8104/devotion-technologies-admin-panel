<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Support\Totp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    /**
     * Setup screen: generates (or resumes) a pending secret and shows the
     * QR provisioning URI. The secret isn't confirmed/active until the
     * admin proves they can generate a valid code from it in confirm().
     */
    public function setup(): View
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin->two_factor_secret || $admin->hasTwoFactorEnabled()) {
            // Fresh secret each time setup is (re)visited without an
            // already-confirmed 2FA, so an abandoned setup attempt can't
            // leave a stale, never-shown secret lying around.
            if (! $admin->hasTwoFactorEnabled()) {
                $admin->two_factor_secret = Totp::generateSecret();
                $admin->save();
            }
        }

        $uri = Totp::provisioningUri($admin->two_factor_secret, $admin->email, config('app.name', 'Admin Panel'));

        return view('backend.auth.two-factor-setup', [
            'admin' => $admin,
            'secret' => $admin->two_factor_secret,
            'provisioningUri' => $uri,
        ]);
    }

    /**
     * Confirms setup: admin enters the current 6-digit code from their
     * authenticator app. On success, 2FA is marked active and recovery
     * codes are generated and shown ONE TIME ONLY.
     */
    public function confirm(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);

        $admin = Auth::guard('admin')->user();

        if (! Totp::verify($admin->two_factor_secret, $request->input('code'))) {
            return back()->withErrors(['code' => 'That code is invalid or has expired. Please try again.']);
        }

        $recoveryCodes = Totp::generateRecoveryCodes();

        $admin->two_factor_recovery_codes = $recoveryCodes;
        $admin->two_factor_confirmed_at = now();
        $admin->save();

        ActivityLog::record('2fa-enabled', 'Auth', $admin->id, 'Two-factor authentication enabled.');

        return redirect()->route('admin.2fa.setup')
            ->with('recovery_codes', $recoveryCodes) // flashed once, view shows them then discards
            ->with('success', 'Two-factor authentication is now enabled. Save your recovery codes somewhere safe.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required|current_password:admin']);

        $admin = Auth::guard('admin')->user();
        $admin->two_factor_secret = null;
        $admin->two_factor_recovery_codes = null;
        $admin->two_factor_confirmed_at = null;
        $admin->save();

        ActivityLog::record('2fa-disabled', 'Auth', $admin->id, 'Two-factor authentication disabled.');

        return redirect()->route('admin.2fa.setup')->with('success', 'Two-factor authentication has been disabled.');
    }

    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin->hasTwoFactorEnabled()) {
            abort(403);
        }

        $codes = Totp::generateRecoveryCodes();
        $admin->two_factor_recovery_codes = $codes;
        $admin->save();

        return redirect()->route('admin.2fa.setup')
            ->with('recovery_codes', $codes)
            ->with('success', 'New recovery codes generated — your old codes no longer work.');
    }

    /**
     * Post-login challenge screen. Reached only via
     * EnsureTwoFactorVerified confining the session here.
     */
    public function challenge(): View|RedirectResponse
    {
        if (! session('admin_needs_2fa')) {
            return redirect()->route('admin.dashboard');
        }

        return view('backend.auth.two-factor-challenge');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);

        $admin = Auth::guard('admin')->user();
        $code = $request->input('code');

        $ok = Totp::verify($admin->two_factor_secret, $code) || $admin->useRecoveryCode($code);

        if (! $ok) {
            return back()->withErrors(['code' => 'That code is invalid. Please try again.']);
        }

        $request->session()->forget('admin_needs_2fa');
        ActivityLog::record('login', 'Auth', $admin->id, 'Admin logged in (2FA verified).');

        return redirect()->intended(route('admin.dashboard'));
    }
}
