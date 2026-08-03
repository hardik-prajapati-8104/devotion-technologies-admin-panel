@php
    $isEdit = isset($blog);
    $existingTags = $isEdit ? $blog->tags->pluck('name')->implode(', ') : '';
@endphp

<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-content" type="button">Content</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-publish" type="button">Publishing</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-seo" type="button">SEO</button></li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="tab-content">
        <div class="row g-3">
            <div class="col-md-8">
                <x-admin.input name="title" label="Blog Title" required="true" :value="$isEdit ? $blog->title : ''" />
            </div>
            <div class="col-md-4">
                <x-admin.select name="blog_category_id" label="Category" placeholder="Select category"
                    :options="$categories->pluck('name', 'id')"
                    :selected="$isEdit ? $blog->blog_category_id : old('blog_category_id')" />
            </div>

            <div class="col-12">
                <label class="form-label small fw-medium">Short Description</label>
                <textarea name="short_description" class="form-control @error('short_description') is-invalid @enderror" rows="2" maxlength="500">{{ old('short_description', $isEdit ? $blog->short_description : '') }}</textarea>
                @error('short_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label small fw-medium">Content</label>
                <textarea name="content" id="content-editor" class="form-control @error('content') is-invalid @enderror" rows="10">{{ old('content', $isEdit ? $blog->content : '') }}</textarea>
                @error('content') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <x-admin.image-upload name="featured_image" label="Featured Image" :existing="$isEdit ? $blog->featured_image : null" />
            </div>
            <div class="col-md-4">
                <x-admin.image-upload name="thumbnail" label="Thumbnail" help="Used in listing cards; falls back to the featured image." :existing="$isEdit ? $blog->thumbnail : null" />
            </div>
            <div class="col-md-4">
                <x-admin.input name="tags" label="Tags" help="Comma-separated, e.g. Laravel, Tutorials, News" value="{{ $existingTags }}" />
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-publish">
        <div class="row g-3">
            <div class="col-md-4">
                <x-admin.select name="status" label="Status" required="true"
                    :options="\App\Models\Blog::STATUSES"
                    :selected="$isEdit ? $blog->status : 'draft'" />
                <div class="form-text">Scheduled posts publish automatically once the publish date passes (wire this up to a scheduled command / cron in Phase 8).</div>
            </div>
            <div class="col-md-4">
                <x-admin.input name="publish_date" label="Publish Date" type="datetime-local"
                    :value="$isEdit && $blog->publish_date ? $blog->publish_date->format('Y-m-d\TH:i') : ''" />
            </div>
            <div class="col-md-4">
                <x-admin.input name="reading_time" label="Reading Time (minutes)" type="number"
                    help="Leave blank to auto-estimate from content length."
                    :value="$isEdit ? $blog->reading_time : ''" />
            </div>
            <div class="col-md-4">
                <div class="form-check mt-4">
                    <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1"
                        {{ old('is_featured', $isEdit ? $blog->is_featured : false) ? 'checked' : '' }}>
                    <label class="form-check-label small" for="is_featured">Mark as Featured Post</label>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-seo">
        <x-admin.seo-fields
            :full="true"
            :seo-title="$isEdit ? $blog->seo_title : null"
            :meta-description="$isEdit ? $blog->meta_description : null"
            :focus-keyword="$isEdit ? $blog->focus_keyword : null"
            :canonical-url="$isEdit ? $blog->canonical_url : null"
            :og-title="$isEdit ? $blog->og_title : null"
            :og-description="$isEdit ? $blog->og_description : null"
            :og-image="$isEdit ? $blog->og_image : null" />
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> {{ $isEdit ? 'Update' : 'Save' }}</button>
    @if ($isEdit)
        <a href="{{ route('admin.blogs.preview', $blog->id) }}" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-eye me-1"></i> Preview</a>
    @endif
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

@push('scripts')
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    CKEDITOR.replace('content-editor', {
        height: 320,
        removeButtons: 'Anchor',
    });
</script>
@endpush
