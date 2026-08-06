<?php

namespace App\Http\Controllers\Backend\Security;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ActivityLog;
use App\Models\AdminSecuritySetting;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SecuritySettingsController extends Controller
{
    public function index(): View
    {
        $this->authorizeSecurity();

        return view('backend.security.index', [
            'settings' => AdminSecuritySetting::current(),
            'currentKeyFingerprint' => $this->keyFingerprint(config('app.key')),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorizeSecurity();

        $data = $request->validate([
            'two_factor_required'             => 'nullable|boolean',
            'session_timeout_minutes'         => 'required|integer|min:0|max:1440',
            'max_login_attempts'              => 'required|integer|min:1|max:20',
            'lockout_minutes'                 => 'required|integer|min:1|max:1440',
            'ip_rules_enabled'                => 'nullable|boolean',
            'recaptcha_enabled'               => 'nullable|boolean',
            'recaptcha_site_key'              => 'nullable|string|max:255',
            'recaptcha_secret_key'            => 'nullable|string|max:255',
            'password_min_length'             => 'required|integer|min:6|max:64',
            'password_require_upper'          => 'nullable|boolean',
            'password_require_lower'          => 'nullable|boolean',
            'password_require_number'         => 'nullable|boolean',
            'password_require_symbol'         => 'nullable|boolean',
            'password_expiry_days'            => 'required|integer|min:0|max:3650',
            'force_password_change_default'   => 'nullable|boolean',
        ]);

        // Checkboxes that are unchecked simply don't appear in the
        // request at all — cast them explicitly rather than relying on
        // 'nullable|boolean' to fill in a false.
        foreach (['two_factor_required', 'ip_rules_enabled', 'recaptcha_enabled', 'password_require_upper', 'password_require_lower', 'password_require_number', 'password_require_symbol', 'force_password_change_default'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        // Don't overwrite the encrypted secret with blank if the admin
        // left the (intentionally masked) field untouched.
        if (! $request->filled('recaptcha_secret_key')) {
            unset($data['recaptcha_secret_key']);
        }

        AdminSecuritySetting::current()->update($data);

        ActivityLog::record('security-settings-updated', 'Security', Auth::guard('admin')->id(), 'Updated security settings.');

        return back()->with('success', 'Security settings saved.');
    }

    /**
     * Rotates APP_KEY and re-encrypts every column in the app that's
     * stored via Eloquent's `encrypted` cast, using two explicit
     * Encrypter instances (old key / new key) rather than relying on the
     * Crypt facade — the facade's bound singleton wouldn't reflect the
     * new key mid-request, which would otherwise silently corrupt data
     * re-saved after the swap.
     *
     * Recommended to run this during a maintenance window: it forces
     * every currently-logged-in admin to log back in (Laravel's session
     * cookie encryption uses APP_KEY too), and any request that touches
     * an encrypted column between the DB update and the .env write below
     * would race against a still-stale in-memory Crypt facade in OTHER
     * concurrent requests. For anything beyond a small admin panel with
     * light concurrent traffic, do this as an Artisan command in a
     * deploy step instead of a live web request.
     */
    public function rotateKey(Request $request): RedirectResponse
    {
        $this->authorizeSecurity();

        $request->validate(['confirm' => 'required|accepted']);

        $cipher = config('app.cipher', 'AES-256-CBC');
        $oldEncrypter = new Encrypter($this->parseKey(config('app.key')), $cipher);

        $newKeyRaw = 'base64:' . base64_encode(Encrypter::generateKey($cipher));
        $newEncrypter = new Encrypter($this->parseKey($newKeyRaw), $cipher);

        DB::transaction(function () use ($oldEncrypter, $newEncrypter) {
            // Re-encrypt admins.two_factor_secret / two_factor_recovery_codes
            DB::table('admins')->whereNotNull('two_factor_secret')->orderBy('id')->get()->each(function ($row) use ($oldEncrypter, $newEncrypter) {
                $update = [];

                if ($row->two_factor_secret) {
                    $update['two_factor_secret'] = $newEncrypter->encryptString(
                        $oldEncrypter->decryptString($row->two_factor_secret)
                    );
                }
                if ($row->two_factor_recovery_codes) {
                    $update['two_factor_recovery_codes'] = $newEncrypter->encrypt(
                        json_decode($oldEncrypter->decrypt($row->two_factor_recovery_codes), true)
                    );
                }

                if ($update) {
                    DB::table('admins')->where('id', $row->id)->update($update);
                }
            });

            // Re-encrypt admin_security_settings.recaptcha_secret_key
            $settingsRow = DB::table('admin_security_settings')->where('id', 1)->first();
            if ($settingsRow && $settingsRow->recaptcha_secret_key) {
                DB::table('admin_security_settings')->where('id', 1)->update([
                    'recaptcha_secret_key' => $newEncrypter->encrypt(
                        $oldEncrypter->decrypt($settingsRow->recaptcha_secret_key)
                    ),
                ]);
            }
        });

        $this->writeEnvKey($newKeyRaw);
        Artisan::call('config:clear');

        ActivityLog::record('encryption-key-rotated', 'Security', Auth::guard('admin')->id(), 'Rotated application encryption key.');

        // Force every session to re-authenticate — cookies/session data
        // encrypted under the old key are no longer decryptable.
        Auth::guard('admin')->logout();
        $request->session()->invalidate();

        return redirect()->route('admin.login')
            ->with('status', 'Encryption key rotated successfully. All admins, including you, must log back in.');
    }

    private function parseKey(string $key): string
    {
        return Str::startsWith($key, 'base64:') ? base64_decode(substr($key, 7)) : $key;
    }

    private function keyFingerprint(string $key): string
    {
        // Never display the real key — just enough to confirm "yes, this
        // is the same key as before / this is a new one" across visits.
        return strtoupper(substr(hash('sha256', $key), 0, 12));
    }

    private function writeEnvKey(string $newKey): void
    {
        $envPath = base_path('.env');
        $contents = file_get_contents($envPath);

        $contents = preg_match('/^APP_KEY=.*/m', $contents)
            ? preg_replace('/^APP_KEY=.*/m', 'APP_KEY=' . $newKey, $contents)
            : $contents . "\nAPP_KEY={$newKey}\n";

        file_put_contents($envPath, $contents);
    }

    private function authorizeSecurity(): void
    {
        if (!Auth::guard('admin')->user()->can('security.manage')) {
            abort(403);
        }
    }
}
