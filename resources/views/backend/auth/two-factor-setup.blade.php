@extends('backend.layouts.master')

@section('title', 'Two-Factor Authentication')

@section('admin-content')

@if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

@if (session('recovery_codes'))
    <div class="alert alert-warning">
        <strong>Save these recovery codes now — they will not be shown again.</strong>
        <div class="row row-cols-2 g-1 mt-2 font-monospace">
            @foreach (session('recovery_codes') as $code)
                <div class="col">{{ $code }}</div>
            @endforeach
        </div>
    </div>
@endif

<div class="card" style="max-width:520px;">
    <div class="card-body">
        @if ($admin->hasTwoFactorEnabled())
            <p class="text-success"><i class="bi bi-shield-check"></i> Two-factor authentication is enabled on your account.</p>

            <form method="POST" action="{{ route('admin.2fa.recovery-codes') }}" class="mb-3">
                @csrf
                <button class="btn btn-outline-secondary btn-sm">Generate new recovery codes</button>
                <span class="text-muted small d-block mt-1">Invalidates your existing recovery codes.</span>
            </form>

            <form method="POST" action="{{ route('admin.2fa.disable') }}">
                @csrf
                <label class="form-label small">Confirm your password to disable 2FA</label>
                <div class="d-flex gap-2">
                    <input type="password" name="password" class="form-control form-control-sm" required>
                    <button class="btn btn-outline-danger btn-sm">Disable</button>
                </div>
                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </form>
        @else
            <p>Scan this QR code with Google Authenticator, Authy, or any TOTP app:</p>

            <div class="mb-3 p-3 bg-light text-center">
                <div id="totpQr"></div>
            </div>
            {{-- Rendered client-side, not via a third-party QR API — that
                 would mean sending the raw TOTP secret to an external
                 server over the URL, which defeats the point of 2FA. --}}
            <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
            <script>
                new QRCode(document.getElementById('totpQr'), {
                    text: @json($provisioningUri),
                    width: 220,
                    height: 220,
                });
            </script>

            <p class="small text-muted">Can't scan? Enter this key manually: <code>{{ $secret }}</code></p>

            <form method="POST" action="{{ route('admin.2fa.confirm') }}" class="d-flex gap-2">
                @csrf
                <input type="text" name="code" class="form-control" placeholder="6-digit code" required autocomplete="one-time-code">
                <button class="btn btn-primary">Enable</button>
            </form>
            @error('code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        @endif
    </div>
</div>

@endsection
