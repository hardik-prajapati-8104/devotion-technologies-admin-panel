@extends('backend.layouts.master')

@section('title', 'Two-Factor Verification')

@section('admin-content')
<div class="card mx-auto mt-5" style="max-width:400px;">
    <div class="card-body">
        <h5 class="card-title mb-3">Two-Factor Verification</h5>
        <p class="text-muted small">Enter the 6-digit code from your authenticator app, or one of your recovery codes.</p>

        <form method="POST" action="{{ route('admin.2fa.verify') }}">
            @csrf
            <input type="text" name="code" class="form-control mb-2" placeholder="Code" required autofocus autocomplete="one-time-code">
            @error('code')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
            <button class="btn btn-primary w-100">Verify</button>
        </form>

        <form method="POST" action="{{ route('admin.logout') }}" class="mt-2">
            @csrf
            <button class="btn btn-link btn-sm w-100">Cancel and log out</button>
        </form>
    </div>
</div>
@endsection