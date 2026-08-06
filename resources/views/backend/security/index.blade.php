@extends('backend.layouts.master')

@section('title', 'Security Settings')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Security</li>
        </ol>
    </nav>
    <div>
        <h4>Security</h4>
        <p class="subtitle">Panel-wide authentication, access, and encryption controls.</p>
    </div>
@endsection

@section('admin-content')

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="d-flex gap-2 mb-3">
    <a href="{{ route('admin.security.ip-rules') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-shield-lock me-1"></i>IP Whitelist / Blacklist</a>
    <a href="{{ route('admin.security.login-attempts') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-clock-history me-1"></i>Login Attempts</a>
    <a href="{{ route('admin.2fa.setup') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-shield-check me-1"></i>My Two-Factor Setup</a>
</div>

<form method="POST" action="{{ route('admin.security.update') }}">
    @csrf
    @method('PUT')

    <div class="card mb-3">
        <div class="card-header fw-medium">Two-Factor Authentication</div>
        <div class="card-body">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="two_factor_required" value="1" id="tfr" {{ $settings->two_factor_required ? 'checked' : '' }}>
                <label class="form-check-label" for="tfr">Require every admin to set up two-factor authentication</label>
            </div>
            <p class="text-muted small mt-1 mb-0">Individual admins enable their own authenticator app under "My Two-Factor Setup" above — there's nothing further to configure here besides making it mandatory.</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header fw-medium">Session Timeout</div>
        <div class="card-body">
            <label class="form-label small">Log out idle admins after (minutes)</label>
            <input type="number" name="session_timeout_minutes" class="form-control" style="max-width:200px;" min="0" max="1440" value="{{ $settings->session_timeout_minutes }}">
            <div class="form-text">0 disables the idle timeout entirely.</div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header fw-medium">Login Attempts / Lockout</div>
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label small">Max failed attempts before lockout</label>
                <input type="number" name="max_login_attempts" class="form-control" min="1" max="20" value="{{ $settings->max_login_attempts }}">
            </div>
            <div class="col-md-6">
                <label class="form-label small">Lockout duration (minutes)</label>
                <input type="number" name="lockout_minutes" class="form-control" min="1" max="1440" value="{{ $settings->lockout_minutes }}">
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header fw-medium">IP Access Control</div>
        <div class="card-body">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="ip_rules_enabled" value="1" id="ipre" {{ $settings->ip_rules_enabled ? 'checked' : '' }}>
                <label class="form-check-label" for="ipre">Enforce IP whitelist / blacklist</label>
            </div>
            <p class="text-muted small mt-1 mb-0">
                Manage the actual list of allowed/blocked addresses on the
                <a href="{{ route('admin.security.ip-rules') }}">IP Whitelist / Blacklist</a> page.
                If whitelist entries exist, only those addresses may sign in; blacklist entries are always blocked.
            </p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header fw-medium">Google reCAPTCHA</div>
        <div class="card-body row g-3">
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="recaptcha_enabled" value="1" id="rce" {{ $settings->recaptcha_enabled ? 'checked' : '' }}>
                    <label class="form-check-label" for="rce">Require reCAPTCHA on the login form</label>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label small">Site key</label>
                <input type="text" name="recaptcha_site_key" class="form-control" value="{{ $settings->recaptcha_site_key }}">
            </div>
            <div class="col-md-6">
                <label class="form-label small">Secret key</label>
                <input type="password" name="recaptcha_secret_key" class="form-control" placeholder="{{ $settings->recaptcha_secret_key ? '•••••••• (leave blank to keep current)' : '' }}">
            </div>
            <div class="col-12 text-muted small">Uses reCAPTCHA v2 (checkbox). Get keys at <a href="https://www.google.com/recaptcha/admin" target="_blank">google.com/recaptcha/admin</a>.</div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header fw-medium">Password Policy</div>
        <div class="card-body row g-3">
            <div class="col-md-4">
                <label class="form-label small">Minimum length</label>
                <input type="number" name="password_min_length" class="form-control" min="6" max="64" value="{{ $settings->password_min_length }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small">Expires after (days)</label>
                <input type="number" name="password_expiry_days" class="form-control" min="0" max="3650" value="{{ $settings->password_expiry_days }}">
                <div class="form-text">0 = never expires.</div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="force_password_change_default" value="1" id="fpcd" {{ $settings->force_password_change_default ? 'checked' : '' }}>
                    <label class="form-check-label small" for="fpcd">Force change for newly created admins</label>
                </div>
            </div>
            <div class="col-12 d-flex gap-4 flex-wrap">
                @foreach ([
                    'password_require_upper'  => 'Require an uppercase letter',
                    'password_require_lower'  => 'Require a lowercase letter',
                    'password_require_number' => 'Require a number',
                    'password_require_symbol' => 'Require a symbol',
                ] as $field => $label)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="{{ $field }}" value="1" id="{{ $field }}" {{ $settings->$field ? 'checked' : '' }}>
                        <label class="form-check-label small" for="{{ $field }}">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Save Settings</button>
</form>

<div class="card mt-4 border-danger">
    <div class="card-header fw-medium text-danger">Encryption Key</div>
    <div class="card-body">
        <p class="mb-1">Current key fingerprint: <code>{{ $currentKeyFingerprint }}</code></p>
        <p class="text-muted small">
            This is a one-way fingerprint for confirming the key changed — not the key itself, which is never displayed.
            Rotating generates a brand-new <code>APP_KEY</code>, re-encrypts every 2FA secret and stored secret key under it,
            and logs <strong>every</strong> admin (including you) out immediately, since existing sessions were encrypted
            under the old key. Do this during a maintenance window.
        </p>
        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rotateKeyModal">
            Rotate Encryption Key
        </button>
    </div>
</div>

<div class="modal fade" id="rotateKeyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.security.rotate-key') }}">
                @csrf
                <div class="modal-header"><h6 class="modal-title text-danger">Rotate encryption key?</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <p>This will immediately log out every admin, including you. This cannot be undone. Make sure you have a backup of your <code>.env</code> file before proceeding.</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="confirm" value="1" id="confirmRotate" required>
                        <label class="form-check-label" for="confirmRotate">I understand and want to proceed.</label>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-danger btn-sm">Rotate Key & Log Everyone Out</button></div>
            </form>
        </div>
    </div>
</div>

@endsection
