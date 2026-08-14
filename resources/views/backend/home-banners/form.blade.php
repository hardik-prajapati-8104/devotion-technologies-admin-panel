@csrf

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label small fw-medium">Title</label>
        <input type="text" name="title" value="{{ old('title', $banner->title ?? '') }}"
               class="form-control @error('title') is-invalid @enderror" placeholder="e.g. Summer Sale — Up to 50% Off">
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium d-block">Status</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch"
                   {{ old('status', $banner->status ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="statusSwitch">Active</label>
        </div>
    </div>

    <div class="col-12">
        <label class="form-label small fw-medium">Subtitle</label>
        <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle ?? '') }}"
               class="form-control @error('subtitle') is-invalid @enderror" placeholder="A short supporting line under the title">
        @error('subtitle') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-medium">Button Text</label>
        <input type="text" name="button_text" value="{{ old('button_text', $banner->button_text ?? '') }}"
               class="form-control @error('button_text') is-invalid @enderror" placeholder="e.g. Shop Now">
        @error('button_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-medium">Button Link</label>
        <input type="text" name="button_link" value="{{ old('button_link', $banner->button_link ?? '') }}"
               class="form-control @error('button_link') is-invalid @enderror" placeholder="e.g. /services or https://...">
        @error('button_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-medium">Show From</label>
        <input type="datetime-local" name="starts_at"
               value="{{ old('starts_at', isset($banner->starts_at) ? $banner->starts_at->format('Y-m-d\TH:i') : '') }}"
               class="form-control @error('starts_at') is-invalid @enderror">
        <div class="form-text">Leave blank to show immediately.</div>
        @error('starts_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-medium">Show Until</label>
        <input type="datetime-local" name="ends_at"
               value="{{ old('ends_at', isset($banner->ends_at) ? $banner->ends_at->format('Y-m-d\TH:i') : '') }}"
               class="form-control @error('ends_at') is-invalid @enderror">
        <div class="form-text">Leave blank to never expire.</div>
        @error('ends_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label small fw-medium">Banner Image {{ isset($banner) ? '' : '(required)' }}</label>
        <div class="d-flex align-items-center gap-3">
            <div class="border rounded d-flex align-items-center justify-content-center flex-shrink-0" style="width:160px; height:80px; overflow:hidden; background:#f8f9fa;">
                <img id="bannerPreview" src="{{ $banner->image_url ?? '' }}"
                     class="{{ ($banner->image_url ?? '') ? '' : 'd-none' }}"
                     style="width:100%; height:100%; object-fit:cover;">
                <i id="bannerPlaceholder" class="bi bi-image text-muted {{ ($banner->image_url ?? '') ? 'd-none' : '' }}" style="font-size:1.5rem;"></i>
            </div>
            <div class="flex-grow-1">
                <input type="file" name="image" id="bannerInput" accept="image/png,image/jpeg,image/webp"
                       class="form-control @error('image') is-invalid @enderror" {{ isset($banner) ? '' : 'required' }}>
                @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-text">
                    Recommended: 1920×600px, JPG/PNG/WEBP, max 4MB.
                    @if (isset($banner) && $banner->image_url)
                        Leave empty to keep the current image.
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const input = document.getElementById('bannerInput');
        const preview = document.getElementById('bannerPreview');
        const placeholder = document.getElementById('bannerPlaceholder');
        if (!input) return;

        input.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                if (placeholder) placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        });
    })();
</script>
