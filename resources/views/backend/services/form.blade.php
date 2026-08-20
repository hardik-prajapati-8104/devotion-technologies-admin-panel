@php $isEdit = isset($service); @endphp

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-general" type="button">General</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-seo" type="button">SEO</button></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="tab-general">
        <div class="row g-3">
            <div class="col-md-6">
                <x-admin.input name="name" label="Service Name" required="true" :value="$isEdit ? $service->name : ''" />
            </div>
            <div class="col-md-6">
                <x-admin.select name="service_category_id" label="Category" placeholder="Select category"
                    :options="$categories->pluck('name', 'id')"
                    :selected="$isEdit ? $service->service_category_id : old('service_category_id')" />
            </div>

            <div class="col-md-12">
                <label class="form-label small fw-medium">Short Description</label>
                <textarea name="short_description" class="form-control @error('short_description') is-invalid @enderror" rows="3" maxlength="500">{{ old('short_description', $isEdit ? $service->short_description : '') }}</textarea>
                @error('short_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-12">
                <label class="form-label small fw-medium">Full Description</label>
                <textarea id="full_description" name="full_description" class="form-control @error('full_description') is-invalid @enderror" rows="3">{{ old('full_description', $isEdit ? $service->full_description : '') }}</textarea>
                @error('full_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <x-admin.image-upload name="featured_image" label="Featured Image" :existing="$isEdit ? $service->featured_image : null" />
            </div>
            <div class="col-md-4">
                <x-admin.input name="icon" label="Icon Class" help="Bootstrap Icons class, e.g. bi-code-slash" :value="$isEdit ? $service->icon : ''" />
            </div>
            <div class="col-md-4">
                <x-admin.input name="display_order" label="Display Order" type="number" :value="$isEdit ? $service->display_order : 0" />
            </div>

            <div class="col-md-4">
                <x-admin.select name="status" label="Status" required="true"
                    :options="[1 => 'Published', 0 => 'Unpublished']"
                    :selected="$isEdit ? (int) $service->status : 1" />
            </div>

            <div class="col-md-4">
                <label class="form-label small fw-medium">Brochure (PDF)</label>
                <input type="file" name="brochure" accept="application/pdf"
                    class="form-control @error('brochure') is-invalid @enderror">
                @error('brochure') <div class="invalid-feedback">{{ $message }}</div> @enderror

                @if ($isEdit && $service->brochure)
                    <div class="mt-2 d-flex align-items-center gap-2">
                        <a href="{{ asset('storage/' . $service->brochure) }}" target="_blank" class="small">
                            <i class="bi bi-file-earmark-pdf text-danger me-1"></i> View current brochure
                        </a>
                        <div class="form-check ms-2">
                            <input type="checkbox" class="form-check-input" id="remove_brochure" name="remove_brochure" value="1">
                            <label class="form-check-label small text-danger" for="remove_brochure">Remove</label>
                        </div>
                    </div>
                @endif
                <small class="text-muted">PDF only, max 5MB.</small>
            </div>

            <div class="col-md-4 d-flex align-items-center">
                <div class="form-check mt-4">
                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1"
                        {{ old('is_featured', $isEdit ? $service->is_featured : false) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="is_featured">Mark as Featured Service</label>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-seo">
        <x-admin.seo-fields
            :seo-title="$isEdit ? $service->seo_title : null"
            :meta-description="$isEdit ? $service->meta_description : null"
            :og-image="$isEdit ? $service->og_image : null" />
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> {{ $isEdit ? 'Update' : 'Save' }}</button>
    <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
    (function () {
        const textarea = document.querySelector('#full_description');
        if (!textarea) return;

        let editorInstance = null;

        ClassicEditor
            .create(textarea, {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'underline', 'link', '|',
                    'bulletedList', 'numberedList', '|',
                    'outdent', 'indent', '|',
                    'blockQuote', 'insertTable', 'undo', 'redo'
                ]
            })
            .then(editor => {
                editorInstance = editor;

                // Keep the underlying textarea in sync on every change
                editor.model.document.on('change:data', () => {
                    editor.updateSourceElement();
                });
            })
            .catch(error => {
                console.error('CKEditor failed to initialize:', error);
            });

        // Safety net: force-sync right before the form submits,
        // in case the change event above hasn't fired yet.
        const form = textarea.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                if (editorInstance) {
                    editorInstance.updateSourceElement();
                }
            });
        }
    })();
</script>
@endpush