@php $isEdit = isset($category); @endphp

<div class="row g-3">
    <div class="col-md-6">
        <x-admin.input name="name" label="Category Name" required="true" :value="$isEdit ? $category->name : ''" />
    </div>
    <div class="col-md-6">
        <x-admin.select name="status" label="Status" required="true"
            :options="[1 => 'Active', 0 => 'Inactive']"
            :selected="$isEdit ? (int) $category->status : 1" />
    </div>
    <div class="col-12">
        <label class="form-label small fw-medium">Description</label>
        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $isEdit ? $category->description : '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <x-admin.input name="seo_title" label="SEO Title" :value="$isEdit ? $category->seo_title : ''" />
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-medium">Meta Description</label>
        <textarea name="meta_description" class="form-control" rows="2">{{ old('meta_description', $isEdit ? $category->meta_description : '') }}</textarea>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> {{ $isEdit ? 'Update' : 'Save' }}</button>
    <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>
