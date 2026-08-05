@csrf

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label small fw-medium">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" value="{{ old('title', $notice->title ?? '') }}"
               class="form-control @error('title') is-invalid @enderror" placeholder="e.g. New leave policy for Editors" required>
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium">Status <span class="text-danger">*</span></label>
        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="draft" {{ old('status', $notice->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ old('status', $notice->status ?? '') === 'published' ? 'selected' : '' }}>Published</option>
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label small fw-medium">Message <span class="text-danger">*</span></label>
        <textarea name="body" rows="6" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $notice->body ?? '') }}</textarea>
        @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-8">
        <label class="form-label small fw-medium">Audience</label>
        <select name="target_roles[]" class="form-select" multiple size="4">
            @php $selected = old('target_roles', $notice->target_roles ?? []); @endphp
            @foreach ($roles as $role)
                <option value="{{ $role }}" {{ in_array($role, $selected ?? []) ? 'selected' : '' }}>{{ ucfirst(str_replace('-', ' ', $role)) }}</option>
            @endforeach
        </select>
        <div class="form-text">Leave nothing selected to send this notice to everyone.</div>
    </div>

    <div class="col-md-2">
        <label class="form-label small fw-medium">Publish Date</label>
        <input type="datetime-local" name="published_at"
               value="{{ old('published_at', isset($notice->published_at) ? $notice->published_at->format('Y-m-d\TH:i') : '') }}"
               class="form-control @error('published_at') is-invalid @enderror">
        @error('published_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-2">
        <label class="form-label small fw-medium">Expires On</label>
        <input type="datetime-local" name="expires_at"
               value="{{ old('expires_at', isset($notice->expires_at) ? $notice->expires_at->format('Y-m-d\TH:i') : '') }}"
               class="form-control @error('expires_at') is-invalid @enderror">
        @error('expires_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
