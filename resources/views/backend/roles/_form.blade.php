@php
    $isEdit = isset($role);
@endphp

<div class="mb-4 col-md-6">
    <label class="form-label small fw-medium" for="name">Role Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
           value="{{ old('name', $isEdit ? $role->name : '') }}" placeholder="e.g. content-editor">
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<label class="form-label small fw-medium d-block">Permissions <span class="text-danger">*</span></label>
@error('permissions') <div class="text-danger small mb-2">{{ $message }}</div> @enderror

<div class="row g-3">
    @foreach ($permissions as $module => $modulePermissions)
        <div class="col-md-4">
            <div class="border rounded p-3 h-100">
                <div class="form-check mb-2 border-bottom pb-2">
                    <input type="checkbox" class="form-check-input select-all-toggle" data-module="{{ $module }}">
                    <label class="form-check-label fw-semibold text-capitalize">{{ str_replace('-', ' ', $module) }}</label>
                </div>
                @foreach ($modulePermissions as $permission)
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input module-{{ $module }}" name="permissions[]"
                               value="{{ $permission->name }}" id="perm-{{ $permission->id }}"
                               {{ in_array($permission->name, old('permissions', $assigned ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label small" for="perm-{{ $permission->id }}">
                            {{ str_replace($module.'.', '', $permission->name) }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-save me-1"></i> {{ $isEdit ? 'Update' : 'Save' }}
    </button>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>

@once
    @push('scripts')
    <script>
        document.querySelectorAll('.select-all-toggle').forEach(function (toggle) {
            toggle.addEventListener('change', function () {
                document.querySelectorAll('.module-' + this.dataset.module).forEach(function (cb) {
                    cb.checked = toggle.checked;
                });
            });
        });
    </script>
    @endpush
@endonce
