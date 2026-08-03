@extends('backend.layouts.master')

@section('title', 'My Profile')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Profile</li>
        </ol>
    </nav>
    <h4>My Profile</h4>
    <p class="subtitle">Update your personal details and profile photo.</p>
@endsection

@section('admin-content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Profile Information</h6>
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">First Name</label>
                            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $admin->first_name) }}">
                            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Last Name</label>
                            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $admin->last_name) }}">
                            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $admin->email) }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Mobile Number</label>
                            <input type="text" name="mobile_number" class="form-control @error('mobile_number') is-invalid @enderror" value="{{ old('mobile_number', $admin->mobile_number) }}">
                            @error('mobile_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium">Profile Image</label>
                            <input type="file" name="profile_image" class="form-control @error('profile_image') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
                            @error('profile_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="bi bi-save me-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card text-center">
            <div class="card-body">
                <img src="{{ $admin->profile_image ? url('public/storage/'.$admin->profile_image) : 'https://ui-avatars.com/api/?background=aa8038&color=fff&size=128&name='.urlencode($admin->name) }}"
                     class="rounded-circle mb-3" width="96" height="96" style="object-fit:cover;">
                <h6 class="fw-semibold mb-0">{{ $admin->name }}</h6>
                <p class="text-muted small mb-3">{{ $admin->email }}</p>
                <a href="{{ route('admin.profile.change-password') }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-shield-lock me-1"></i> Change Password
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
