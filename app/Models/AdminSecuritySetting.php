<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSecuritySetting extends Model
{
    protected $fillable = [
        'two_factor_required',
        'session_timeout_minutes',
        'max_login_attempts',
        'lockout_minutes',
        'ip_rules_enabled',
        'recaptcha_enabled',
        'recaptcha_site_key',
        'recaptcha_secret_key',
        'password_min_length',
        'password_require_upper',
        'password_require_lower',
        'password_require_number',
        'password_require_symbol',
        'password_expiry_days',
        'force_password_change_default',
    ];

    protected $casts = [
        'two_factor_required'            => 'boolean',
        'ip_rules_enabled'               => 'boolean',
        'recaptcha_enabled'              => 'boolean',
        'password_require_upper'         => 'boolean',
        'password_require_lower'         => 'boolean',
        'password_require_number'        => 'boolean',
        'password_require_symbol'        => 'boolean',
        'force_password_change_default'  => 'boolean',
        'recaptcha_secret_key'           => 'encrypted',
    ];

    /**
     * Every read/write goes through this single row (id=1). Using a real
     * model instead of a generic key/value settings table keeps every
     * field typed, cast, and validated like any other Eloquent attribute,
     * and keeps call sites simple: AdminSecuritySetting::current()->foo.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }

    /**
     * Builds the Laravel validation rule array for a new admin password
     * from the currently configured policy.
     */
    public function passwordRules(): array
    {
        $rules = ['required', 'string', 'confirmed', "min:{$this->password_min_length}"];

        if ($this->password_require_upper) {
            $rules[] = 'regex:/[A-Z]/';
        }
        if ($this->password_require_lower) {
            $rules[] = 'regex:/[a-z]/';
        }
        if ($this->password_require_number) {
            $rules[] = 'regex:/[0-9]/';
        }
        if ($this->password_require_symbol) {
            $rules[] = 'regex:/[^A-Za-z0-9]/';
        }

        return $rules;
    }

    public function passwordPolicyDescription(): string
    {
        $parts = ["at least {$this->password_min_length} characters"];
        if ($this->password_require_upper) $parts[] = 'an uppercase letter';
        if ($this->password_require_lower) $parts[] = 'a lowercase letter';
        if ($this->password_require_number) $parts[] = 'a number';
        if ($this->password_require_symbol) $parts[] = 'a symbol';

        return 'Password must contain ' . implode(', ', $parts) . '.';
    }
}
