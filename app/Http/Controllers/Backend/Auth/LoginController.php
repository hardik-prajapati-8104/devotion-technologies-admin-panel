<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AdminIpRule;
use App\Models\AdminLoginAttempt;
use App\Models\AdminSecuritySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('backend.auth.login', [
            'settings' => AdminSecuritySetting::current(),
        ]);
    }

    /**
     * Handle an admin login attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $settings = AdminSecuritySetting::current();
        $ip = $request->ip();

        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->input('email');

        // 1. IP blacklist/whitelist — checked before anything else touches
        // credentials, so a blocked IP can't even probe for valid emails.
        if ($settings->ip_rules_enabled) {
            if (AdminIpRule::isBlacklisted($ip)) {
                $this->logAttempt($email, $ip, $request, false, 'ip_blocked');
                throw ValidationException::withMessages(['email' => 'Access from your network is not permitted.']);
            }
            if (! AdminIpRule::isWhitelisted($ip)) {
                $this->logAttempt($email, $ip, $request, false, 'ip_not_whitelisted');
                throw ValidationException::withMessages(['email' => 'Access from your network is not permitted.']);
            }
        }

        // 2. Google reCAPTCHA (v2 checkbox — adjust the response field name
        // if you switch to v3/Enterprise).
        if ($settings->recaptcha_enabled && ! $this->verifyRecaptcha($request, $settings)) {
            $this->logAttempt($email, $ip, $request, false, 'recaptcha_failed');
            throw ValidationException::withMessages(['recaptcha' => 'reCAPTCHA verification failed. Please try again.']);
        }

        // 3. Lockout — checked against BOTH the cache-backed RateLimiter
        // (fast, per IP+email, resets on cache flush) and the persistent
        // admin_login_attempts log (survives cache flush/restart, and is
        // what the "Login Attempts" admin page reports from).
        $throttleKey = Str::lower($email) . '|' . $ip;
        $seconds = RateLimiter::availableIn($throttleKey);

        if (RateLimiter::tooManyAttempts($throttleKey, $settings->max_login_attempts)) {

            if ($seconds > 0) {

                $this->logAttempt($email, $ip, $request, false, 'locked_out');

                throw ValidationException::withMessages([
                    'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
                ]);
            }

            RateLimiter::clear($throttleKey);
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (! Auth::guard('admin')->attempt(array_merge($credentials, ['status' => 1, 'login' => 1]), $remember)) {
            RateLimiter::hit($throttleKey, $settings->lockout_minutes * 60);
            $this->logAttempt($email, $ip, $request, false, 'invalid_credentials');

            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records, or your account is inactive / not permitted to log in.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();
        $request->session()->put('admin_last_activity', now());

        $admin = Auth::guard('admin')->user();

        // 4. Hand off to 2FA challenge if this admin has it enabled — the
        // EnsureTwoFactorVerified middleware confines them to the
        // challenge route until they verify, so we log the attempt as
        // successful here (password check passed) but don't grant full
        // access yet.
        if ($admin->hasTwoFactorEnabled()) {
            $request->session()->put('admin_needs_2fa', true);
            $this->logAttempt($email, $ip, $request, true, 'password_ok_awaiting_2fa');

            return redirect()->route('admin.2fa.challenge');
        }

        $this->logAttempt($email, $ip, $request, true);
        ActivityLog::record('login', 'Auth', $admin->id, 'Admin logged in.');

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Log the admin out.
     */
    public function logout(Request $request): RedirectResponse
    {
        ActivityLog::record('logout', 'Auth', Auth::guard('admin')->id(), 'Admin logged out.');

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    private function logAttempt(string $email, string $ip, Request $request, bool $successful, ?string $reason = null): void
    {
        AdminLoginAttempt::log($email, $ip, $request->userAgent(), $successful, $reason);
    }

    /**
     * Verifies a v2 ("I'm not a robot" checkbox) response against
     * Google's siteverify endpoint. Swap the payload/response field if
     * you're on v3 or reCAPTCHA Enterprise instead.
     */
    private function verifyRecaptcha(Request $request, AdminSecuritySetting $settings): bool
    {
        $token = $request->input('g-recaptcha-response');

        if (! $token) {
            return false;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => $settings->recaptcha_secret_key,
            'response' => $token,
            'remoteip' => $request->ip(),
        ]);

        return (bool) ($response->json('success') ?? false);
    }
}