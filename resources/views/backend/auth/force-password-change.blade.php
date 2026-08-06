@extends('backend.layouts.master')

@section('title', 'Update Your Password')

@section('admin-content')
<div class="card mx-auto mt-5" style="max-width:420px;">
    <div class="card-body">
        <h5 class="card-title mb-1">Update Your Password</h5>
        <p class="text-muted small mb-3">Your password must be changed before you can continue.</p>

        <form method="POST" action="{{ route('admin.password.update') }}">
            @csrf
            <div class="mb-2">
                <label class="form-label small">Current password</label>
                <input type="password" name="current_password" class="form-control" required>
                @error('current_password')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="mb-2">
                <label class="form-label small">New password</label>
                <input type="password" name="password" class="form-control" required>
                <div class="form-text">{{ $policyDescription }}</div>
                @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label small">Confirm new password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100">Update Password</button>
        </form>
    </div>
</div>
@endsection