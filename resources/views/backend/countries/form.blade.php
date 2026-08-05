@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label small fw-medium">Country Name <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ old('name', $country->name ?? '') }}"
               class="form-control @error('name') is-invalid @enderror" placeholder="e.g. India" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label small fw-medium">Country Code <span class="text-danger">*</span></label>
        <input type="text" name="code" value="{{ old('code', $country->code ?? '') }}"
               class="form-control text-uppercase @error('code') is-invalid @enderror" placeholder="e.g. IN" maxlength="5" required>
        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">ISO 2–3 letter code.</div>
    </div>

    <div class="col-md-3">
        <label class="form-label small fw-medium">Phone Code</label>
        <input type="text" name="phone_code" value="{{ old('phone_code', $country->phone_code ?? '') }}"
               class="form-control @error('phone_code') is-invalid @enderror" placeholder="e.g. +91">
        @error('phone_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-medium">Flag Image</label>
        <div class="d-flex align-items-center gap-3">
            <div class="border rounded d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:64px; height:44px; overflow:hidden; background:#f8f9fa;">
                <img id="flagPreview" src="{{ $country->flag_url ?? '' }}"
                     class="{{ ($country->flag_url ?? '') ? '' : 'd-none' }}"
                     style="width:100%; height:100%; object-fit:cover;">
                <i id="flagPlaceholder" class="bi bi-flag text-muted {{ ($country->flag_url ?? '') ? 'd-none' : '' }}"></i>
            </div>
            <div class="flex-grow-1">
                <input type="file" name="flag" id="flagInput" accept="image/png,image/jpeg,image/webp"
                       class="form-control @error('flag') is-invalid @enderror">
                @error('flag') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-text">JPG, PNG, or WEBP. Max 4MB.
                    @if ($country->flag ?? false)
                        Leave empty to keep the current flag.
                    @endif
                </div>
            </div>
        </div>

        @if ($country->flag ?? false)
            <input type="hidden" name="remove_flag" id="removeFlagInput" value="0">
            <div class="form-check mt-2">
                <input type="checkbox" class="form-check-input" id="removeFlagCheck">
                <label class="form-check-label small text-danger" for="removeFlagCheck">Remove current flag image</label>
            </div>
        @endif
    </div>

    <div class="col-md-3">
        <label class="form-label small fw-medium">Sort Order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $country->sort_order ?? 0) }}"
               class="form-control @error('sort_order') is-invalid @enderror" min="0">
        @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label small fw-medium d-block">Status</label>
        <div class="form-check form-switch mt-2">
            <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch"
                   {{ old('status', $country->status ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="statusSwitch">Active</label>
        </div>
    </div>
</div>

<script>
    (function () {
        const input = document.getElementById('flagInput');
        const preview = document.getElementById('flagPreview');
        const placeholder = document.getElementById('flagPlaceholder');
        const removeCheck = document.getElementById('removeFlagCheck');
        const removeInput = document.getElementById('removeFlagInput');

        if (input) {
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

                // Picking a new file overrides "remove" intent.
                if (removeCheck) {
                    removeCheck.checked = false;
                    removeInput.value = '0';
                }
            });
        }

        if (removeCheck) {
            removeCheck.addEventListener('change', function () {
                removeInput.value = this.checked ? '1' : '0';
                if (this.checked) {
                    preview.classList.add('d-none');
                    if (placeholder) placeholder.classList.remove('d-none');
                    input.value = '';
                }
            });
        }
    })();
</script>