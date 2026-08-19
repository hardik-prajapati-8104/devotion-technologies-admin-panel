<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public $user;

    private const GENERAL_KEYS = ['site_name', 'site_email', 'site_phone', 'site_address', 'copyright_text', 'site_logo', 'site_favicon'];
    private const SOCIAL_KEYS  = ['social_facebook', 'social_instagram', 'social_linkedin', 'social_youtube', 'social_twitter'];
    private const CONTACT_KEYS = ['contact_email', 'contact_phone', 'contact_whatsapp', 'contact_address', 'contact_map_url'];
    private const SMTP_KEYS    = ['smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'smtp_from_email', 'smtp_from_name'];

    public function __construct(private ImageUploadService $imageUploadService)
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    /**
     * All keys managed by the fixed tabs (General / Social / Contact / SMTP).
     * Anything outside this list is treated as a "custom" (dynamic) setting
     * and becomes editable through the Custom Settings CRUD tab.
     */
    private function reservedKeys(): array
    {
        return array_merge(self::GENERAL_KEYS, self::SOCIAL_KEYS, self::CONTACT_KEYS, self::SMTP_KEYS);
    }

    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('settings.view')) {
            abort(403, 'Sorry !! You are unauthorized to view settings !');
        }

        $settings = Setting::pluck('value', 'key');

        // Full model instances (need id + group) for the dynamic CRUD table.
        $customSettings = Setting::whereNotIn('key', $this->reservedKeys())
            ->orderBy('group')
            ->orderBy('key')
            ->get();

        return view('backend.settings.index', compact('settings', 'customSettings'));
    }

    public function updateGeneral(Request $request)
    {
        $this->authorizeEdit();

        $data = $request->validate([
            'site_name'      => 'required|string|max:150',
            'site_email'     => 'nullable|email|max:150',
            'site_phone'     => 'nullable|string|max:30',
            'site_address'   => 'nullable|string|max:500',
            'copyright_text' => 'nullable|string|max:255',
            'site_logo'      => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:1024',
            'site_favicon'   => 'nullable|image|mimes:jpg,jpeg,png,ico|max:256',
        ]);

        foreach (['site_name', 'site_email', 'site_phone', 'site_address', 'copyright_text'] as $key) {
            Setting::set($key, $data[$key] ?? null, 'general');
        }

        if ($request->hasFile('site_logo')) {
            Setting::set(
                'site_logo',
                $this->imageUploadService->replace($request->file('site_logo'), 'settings', Setting::get('site_logo')),
                'general'
            );
        }

        if ($request->hasFile('site_favicon')) {
            Setting::set(
                'site_favicon',
                $this->imageUploadService->replace($request->file('site_favicon'), 'settings', Setting::get('site_favicon')),
                'general'
            );
        }

        ActivityLog::record('updated', 'Settings', null, 'Updated general site settings.');

        return $this->savedRedirect('General settings updated !!');
    }

    public function updateSocial(Request $request)
    {
        $this->authorizeEdit();

        $data = $request->validate([
            'social_facebook'  => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_linkedin'  => 'nullable|url|max:255',
            'social_youtube'   => 'nullable|url|max:255',
            'social_twitter'   => 'nullable|url|max:255',
        ]);

        foreach (self::SOCIAL_KEYS as $key) {
            Setting::set($key, $data[$key] ?? null, 'social');
        }

        ActivityLog::record('updated', 'Settings', null, 'Updated social media links.');

        return $this->savedRedirect('Social media links updated !!');
    }

    public function updateContact(Request $request)
    {
        $this->authorizeEdit();

        $data = $request->validate([
            'contact_email'    => 'nullable|email|max:150',
            'contact_phone'    => 'nullable|string',
            'contact_whatsapp' => 'nullable|string|max:30',
            'contact_address'  => 'nullable|string|max:500',
            'contact_map_url'  => 'nullable|url|max:500',
        ]);

        foreach (self::CONTACT_KEYS as $key) {
            Setting::set($key, $data[$key] ?? null, 'contact');
        }

        ActivityLog::record('updated', 'Settings', null, 'Updated contact settings.');

        return $this->savedRedirect('Contact settings updated !!');
    }

    /**
     * SMTP password is encrypted at rest and never redisplayed in the
     * form. Leaving the field blank on submit keeps the existing
     * encrypted value untouched.
     */
    public function updateSmtp(Request $request)
    {
        $this->authorizeEdit();

        $data = $request->validate([
            'smtp_host'       => 'nullable|string|max:150',
            'smtp_port'       => 'nullable|integer|min:1|max:65535',
            'smtp_username'   => 'nullable|string|max:150',
            'smtp_password'   => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|in:tls,ssl,none',
            'smtp_from_email' => 'nullable|email|max:150',
            'smtp_from_name'  => 'nullable|string|max:150',
        ]);

        foreach (['smtp_host', 'smtp_username', 'smtp_encryption', 'smtp_from_email', 'smtp_from_name'] as $key) {
            Setting::set($key, $data[$key] ?? null, 'smtp');
        }

        Setting::set('smtp_port', $data['smtp_port'] ?? 587, 'smtp');

        if ($request->filled('smtp_password')) {
            Setting::set('smtp_password', Crypt::encryptString($data['smtp_password']), 'smtp');
        }

        ActivityLog::record('updated', 'Settings', null, 'Updated SMTP configuration.');

        return $this->savedRedirect('SMTP settings updated !! (Password left blank keeps the current one.)');
    }

    /*
    |--------------------------------------------------------------------------
    | Dynamic "Custom Settings" CRUD
    |--------------------------------------------------------------------------
    | Lets admins create, edit, and delete arbitrary key/value settings from
    | the UI without a developer needing to wire up a new field/tab in code
    | every time a new config value is needed.
    */

    public function storeCustom(Request $request)
    {
        $this->authorizeEdit();

        $data = $request->validate([
            'key' => [
                'required', 'string', 'max:100',
                'regex:/^[a-z][a-z0-9_]*$/', // lowercase snake_case, starts with a letter
                Rule::notIn($this->reservedKeys()),
                Rule::unique('settings', 'key'),
            ],
            'value' => 'nullable|string|max:2000',
            'group' => 'nullable|string|max:50',
        ], [
            'key.regex'  => 'Key must be lowercase snake_case (e.g. maintenance_mode) and start with a letter.',
            'key.not_in' => 'That key is reserved by a built-in setting.',
            'key.unique' => 'A setting with that key already exists.',
        ]);

        Setting::set($data['key'], $data['value'] ?? null, $data['group'] ?: 'custom');

        ActivityLog::record('created', 'Settings', null, "Created custom setting [{$data['key']}].");

        return $this->savedRedirect('Custom setting created !!');
    }

    public function updateCustom(Request $request, Setting $setting)
    {
        $this->authorizeEdit();

        if (in_array($setting->key, $this->reservedKeys(), true)) {
            abort(403, 'This setting is managed by a built-in tab and cannot be edited here.');
        }

        $data = $request->validate([
            'key' => [
                'required', 'string', 'max:100',
                'regex:/^[a-z][a-z0-9_]*$/',
                Rule::notIn($this->reservedKeys()),
                Rule::unique('settings', 'key')->ignore($setting->id),
            ],
            'value' => 'nullable|string|max:2000',
            'group' => 'nullable|string|max:50',
        ]);

        $setting->update([
            'key'   => $data['key'],
            'value' => $data['value'] ?? null,
            'group' => $data['group'] ?: 'custom',
        ]);

        Cache::forget('settings.all');

        ActivityLog::record('updated', 'Settings', null, "Updated custom setting [{$setting->key}].");

        return $this->savedRedirect('Custom setting updated !!');
    }

    public function destroyCustom(Setting $setting)
    {
        $this->authorizeEdit();

        if (in_array($setting->key, $this->reservedKeys(), true)) {
            abort(403, 'This setting is managed by a built-in tab and cannot be deleted here.');
        }

        $key = $setting->key;
        $setting->delete();
        Cache::forget('settings.all');

        ActivityLog::record('deleted', 'Settings', null, "Deleted custom setting [{$key}].");

        return $this->savedRedirect('Custom setting deleted !!');
    }

    private function authorizeEdit(): void
    {
        if (is_null($this->user) || ! $this->user->can('settings.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit settings !');
        }
    }

    private function savedRedirect(string $message)
    {
        session()->flash('success', $message);
        return redirect()->route('admin.settings.index');
    }
}
