@php
    $isEdit = isset($admin);
    $assignedRoles = $isEdit ? $admin->roles->pluck('name')->all() : [];
@endphp

<div class="row g-3">

    <div class="col-md-4">
        <label class="form-label small fw-medium" for="first_name">First Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name"
               value="{{ old('first_name', $isEdit ? $admin->first_name : '') }}" placeholder="Enter first name">
        @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium" for="last_name">Last Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name"
               value="{{ old('last_name', $isEdit ? $admin->last_name : '') }}" placeholder="Enter last name">
        @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium" for="username">Username <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username"
               value="{{ old('username', $isEdit ? $admin->username : '') }}" placeholder="Enter username">
        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium" for="email">Email <span class="text-danger">*</span></label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
               value="{{ old('email', $isEdit ? $admin->email : '') }}" placeholder="you@devotiontechnology.com">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium" for="mobile_number">Mobile Number</label>
        <input type="text" class="form-control @error('mobile_number') is-invalid @enderror" id="mobile_number" name="mobile_number"
               value="{{ old('mobile_number', $isEdit ? $admin->mobile_number : '') }}" placeholder="Enter mobile number">
        @error('mobile_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium" for="profile_image">Profile Image</label>
        <input type="file" class="form-control @error('profile_image') is-invalid @enderror" id="profile_image" name="profile_image" accept="image/png,image/jpeg,image/webp">
        @error('profile_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        @if ($isEdit && $admin->profile_image)
            <img src="{{ asset('storage/'.$admin->profile_image) }}" class="rounded mt-2" width="56" height="56" style="object-fit:cover;">
        @endif
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium" for="password">Password {!! $isEdit ? '' : '<span class="text-danger">*</span>' !!}</label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
               placeholder="{{ $isEdit ? 'Leave blank to keep current password' : 'Enter password' }}">
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium" for="password_confirmation">Confirm Password</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Re-enter password">
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium" for="roles">Assign Roles <span class="text-danger">*</span></label>
        <select name="roles[]" id="roles" class="form-select @error('roles') is-invalid @enderror" multiple>
            @foreach ($roles as $role)
                <option value="{{ $role->name }}" {{ in_array($role->name, old('roles', $assignedRoles)) ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>
        @error('roles') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">Hold Ctrl / Cmd to select multiple roles.</div>
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium" for="status">Status</label>
        <select class="form-select" id="status" name="status">
            <option value="1" {{ old('status', $isEdit ? (int) $admin->status : 1) == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('status', $isEdit ? (int) $admin->status : 1) == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium" for="login">Login Allowed</label>
        <select class="form-select" id="login" name="login">
            <option value="1" {{ old('login', $isEdit ? (int) $admin->login : 1) == 1 ? 'selected' : '' }}>Allowed</option>
            <option value="0" {{ old('login', $isEdit ? (int) $admin->login : 1) == 0 ? 'selected' : '' }}>Blocked</option>
        </select>
    </div>

</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-save me-1"></i> {{ $isEdit ? 'Update' : 'Save' }}
    </button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>
