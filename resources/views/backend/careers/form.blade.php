@php $isEdit = isset($career); @endphp

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-general" type="button">General</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-details" type="button">Job Details</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-seo" type="button">SEO</button></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="tab-general">
        <div class="row g-3">
            <div class="col-md-8">
                <x-admin.input name="title" label="Job Title" required="true" :value="$isEdit ? $career->title : ''" />
            </div>
            <div class="col-md-4">
                <x-admin.select name="employment_type" label="Employment Type" required="true"
                    :options="\App\Models\Career::EMPLOYMENT_TYPES"
                    :selected="$isEdit ? $career->employment_type : 'full_time'" />
            </div>

            <div class="col-md-4">
                <x-admin.input name="department" label="Department" :value="$isEdit ? $career->department : ''" />
            </div>
            <div class="col-md-4">
                <x-admin.input name="location" label="Location" :value="$isEdit ? $career->location : ''" />
            </div>
            <div class="col-md-4">
                <x-admin.input name="experience" label="Experience" help="e.g. 2-4 years" :value="$isEdit ? $career->experience : ''" />
            </div>

            <div class="col-md-6">
                <x-admin.input name="salary_info" label="Salary Information" help="e.g. ₹6L - ₹9L PA, or Undisclosed" :value="$isEdit ? $career->salary_info : ''" />
            </div>
            <div class="col-md-6">
                <x-admin.input name="application_deadline" label="Application Deadline" type="date"
                    :value="$isEdit && $career->application_deadline ? $career->application_deadline->format('Y-m-d') : ''" />
            </div>

            <div class="col-12">
                <label class="form-label small fw-medium">Short Description</label>
                <textarea name="short_description" class="form-control" rows="2" maxlength="500">{{ old('short_description', $isEdit ? $career->short_description : '') }}</textarea>
            </div>

            <div class="col-md-4">
                <x-admin.select name="status" label="Status" required="true"
                    :options="[1 => 'Open', 0 => 'Closed']"
                    :selected="$isEdit ? (int) $career->status : 1" />
            </div>
            <div class="col-md-4 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1"
                        {{ old('is_featured', $isEdit ? $career->is_featured : false) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="is_featured">Mark as Featured Job</label>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-details">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label small fw-medium">Full Description</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description', $isEdit ? $career->description : '') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label small fw-medium">Responsibilities</label>
                <textarea name="responsibilities" class="form-control" rows="4" placeholder="One per line">{{ old('responsibilities', $isEdit ? $career->responsibilities : '') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label small fw-medium">Requirements</label>
                <textarea name="requirements" class="form-control" rows="4" placeholder="One per line">{{ old('requirements', $isEdit ? $career->requirements : '') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label small fw-medium">Benefits</label>
                <textarea name="benefits" class="form-control" rows="4" placeholder="One per line">{{ old('benefits', $isEdit ? $career->benefits : '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-seo">
        <x-admin.seo-fields
            :show-og-image="false"
            :seo-title="$isEdit ? $career->seo_title : null"
            :meta-description="$isEdit ? $career->meta_description : null" />
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> {{ $isEdit ? 'Update' : 'Save' }}</button>
    <a href="{{ route('admin.careers.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>
