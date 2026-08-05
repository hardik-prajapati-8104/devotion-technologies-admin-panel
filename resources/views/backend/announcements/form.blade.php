@csrf

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label small fw-medium">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" value="{{ old('title', $announcement->title ?? '') }}"
               class="form-control @error('title') is-invalid @enderror" placeholder="e.g. Office closed for Diwali" required>
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="draft" {{ old('status', $announcement->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ old('status', $announcement->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label small fw-medium">Message <span class="text-danger">*</span></label>
        <textarea name="body" rows="6" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $announcement->body ?? '') }}</textarea>
        @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium">Publish Date</label>
        <input type="datetime-local" name="published_at"
               value="{{ old('published_at', isset($announcement->published_at) ? $announcement->published_at->format('Y-m-d\TH:i') : '') }}"
               class="form-control @error('published_at') is-invalid @enderror">
        <div class="form-text">Leave blank to publish immediately.</div>
        @error('published_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium">Expires On</label>
        <input type="datetime-local" name="expires_at"
               value="{{ old('expires_at', isset($announcement->expires_at) ? $announcement->expires_at->format('Y-m-d\TH:i') : '') }}"
               class="form-control @error('expires_at') is-invalid @enderror">
        @error('expires_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium d-block">Pin to top</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="is_pinned" value="1" id="pinSwitch"
                   {{ old('is_pinned', $announcement->is_pinned ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="pinSwitch">Pinned</label>
        </div>
    </div>
</div>
