@php $isEdit = isset($project); @endphp

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-general" type="button">General</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-gallery" type="button">Gallery</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-seo" type="button">SEO</button></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="tab-general">
        <div class="row g-3">
            <div class="col-md-6">
                <x-admin.input name="name" label="Project Name" required="true" :value="$isEdit ? $project->name : ''" />
            </div>
            <div class="col-md-6">
                <x-admin.select name="project_category_id" label="Category" placeholder="Select category"
                    :options="$categories->pluck('name', 'id')"
                    :selected="$isEdit ? $project->project_category_id : old('project_category_id')" />
            </div>

            <div class="col-md-4">
                <x-admin.input name="client_name" label="Client Name" :value="$isEdit ? $project->client_name : ''" />
            </div>
            <div class="col-md-4">
                <x-admin.input name="location" label="Location" :value="$isEdit ? $project->location : ''" />
            </div>
            <div class="col-md-4">
                <x-admin.input name="completion_date" label="Completion Date" type="date"
                    :value="$isEdit && $project->completion_date ? $project->completion_date->format('Y-m-d') : ''" />
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-medium">Short Description</label>
                <textarea name="short_description" class="form-control @error('short_description') is-invalid @enderror" rows="3" maxlength="500">{{ old('short_description', $isEdit ? $project->short_description : '') }}</textarea>
                @error('short_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label small fw-medium">Full Description</label>
                <textarea name="full_description" class="form-control @error('full_description') is-invalid @enderror" rows="3">{{ old('full_description', $isEdit ? $project->full_description : '') }}</textarea>
                @error('full_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <x-admin.input name="project_url" label="Project URL" type="url" :value="$isEdit ? $project->project_url : ''" />
            </div>
            <div class="col-md-4">
                <x-admin.input name="technologies" label="Technologies" help="Comma-separated, e.g. Laravel, Vue, MySQL" :value="$isEdit ? $project->technologies : ''" />
            </div>
            <div class="col-md-4">
                <x-admin.input name="display_order" label="Display Order" type="number" :value="$isEdit ? $project->display_order : 0" />
            </div>

            <div class="col-md-4">
                <x-admin.image-upload name="featured_image" label="Featured Image" :existing="$isEdit ? $project->featured_image : null" />
            </div>
            <div class="col-md-4">
                <x-admin.select name="status" label="Status" required="true"
                    :options="\App\Models\Project::STATUSES"
                    :selected="$isEdit ? $project->status : 'draft'" />
            </div>
            <div class="col-md-4 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1"
                        {{ old('is_featured', $isEdit ? $project->is_featured : false) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="is_featured">Mark as Featured Project</label>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-gallery">
        @if ($isEdit && $project->images->isNotEmpty())
            <label class="form-label small fw-medium d-block">Existing Gallery Images</label>
            <div class="row g-2 mb-3">
                @foreach ($project->images as $image)
                    <div class="col-auto" id="gallery-image-{{ $image->id }}">
                        <div class="position-relative">
                            <img src="{{ asset('storage/'.$image->image_path) }}" width="90" height="90" class="rounded border" style="object-fit:cover;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0 px-1"
                                    onclick="removeGalleryImage({{ $project->id }}, {{ $image->id }})">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <label class="form-label small fw-medium" for="gallery">Add Gallery Images</label>
        <input type="file" class="form-control @error('gallery.*') is-invalid @enderror" id="gallery" name="gallery[]" multiple accept="image/png,image/jpeg,image/webp">
        @error('gallery.*') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">You can select multiple images at once.</div>
    </div>

    <div class="tab-pane fade" id="tab-seo">
        <x-admin.seo-fields
            :seo-title="$isEdit ? $project->seo_title : null"
            :meta-description="$isEdit ? $project->meta_description : null"
            :og-image="$isEdit ? $project->og_image : null" />
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> {{ $isEdit ? 'Update' : 'Save' }}</button>
    <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

@push('scripts')
<script>
    function removeGalleryImage(projectId, imageId) {
        Swal.fire({
            title: 'Remove this image?',
            text: 'This will permanently delete it from the gallery.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#aa8038',
            confirmButtonText: 'Yes, remove it'
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch(`/admin/projects/${projectId}/gallery/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`gallery-image-${imageId}`)?.remove();
                }
            });
        });
    }
</script>
@endpush
