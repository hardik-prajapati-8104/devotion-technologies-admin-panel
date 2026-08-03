@php $isEdit = isset($member); @endphp

<div class="row g-3">
    <div class="col-md-4">
        <x-admin.input name="name" label="Full Name" required="true" :value="$isEdit ? $member->name : ''" />
    </div>
    <div class="col-md-4">
        <x-admin.input name="designation" label="Designation" :value="$isEdit ? $member->designation : ''" />
    </div>
    <div class="col-md-4">
        <x-admin.input name="department" label="Department" :value="$isEdit ? $member->department : ''" />
    </div>

    <div class="col-md-4">
        <x-admin.image-upload name="profile_image" label="Profile Image" :existing="$isEdit ? $member->profile_image : null" />
    </div>
    <div class="col-md-4">
        <x-admin.input name="email" label="Email" type="email" :value="$isEdit ? $member->email : ''" />
    </div>
    <div class="col-md-4">
        <x-admin.input name="phone" label="Phone" :value="$isEdit ? $member->phone : ''" />
    </div>

    <div class="col-12">
        <label class="form-label small fw-medium">Biography</label>
        <textarea name="biography" class="form-control @error('biography') is-invalid @enderror" rows="3" maxlength="3000">{{ old('biography', $isEdit ? $member->biography : '') }}</textarea>
        @error('biography') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <x-admin.input name="linkedin_url" label="LinkedIn URL" type="url" :value="$isEdit ? $member->linkedin_url : ''" />
    </div>
    <div class="col-md-3">
        <x-admin.input name="facebook_url" label="Facebook URL" type="url" :value="$isEdit ? $member->facebook_url : ''" />
    </div>
    <div class="col-md-3">
        <x-admin.input name="instagram_url" label="Instagram URL" type="url" :value="$isEdit ? $member->instagram_url : ''" />
    </div>
    <div class="col-md-3">
        <x-admin.input name="twitter_url" label="Twitter / X URL" type="url" :value="$isEdit ? $member->twitter_url : ''" />
    </div>

    <div class="col-md-4">
        <x-admin.input name="display_order" label="Display Order" type="number" :value="$isEdit ? $member->display_order : 0" />
    </div>
    <div class="col-md-4">
        <x-admin.select name="status" label="Status" required="true"
            :options="[1 => 'Active', 0 => 'Inactive']"
            :selected="$isEdit ? (int) $member->status : 1" />
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> {{ $isEdit ? 'Update' : 'Save' }}</button>
    <a href="{{ route('admin.team.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>
