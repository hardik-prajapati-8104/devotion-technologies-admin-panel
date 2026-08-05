@php $isEdit = isset($testimonial); @endphp

<div class="row g-3">
    <div class="col-md-4">
        <x-admin.input name="client_name" label="Client Name" required="true" :value="$isEdit ? $testimonial->client_name : ''" />
    </div>
    <div class="col-md-4">
        <x-admin.input name="company" label="Company" :value="$isEdit ? $testimonial->company : ''" />
    </div>
    <div class="col-md-4">
        <x-admin.input name="designation" label="Designation" :value="$isEdit ? $testimonial->designation : ''" />
    </div>

    <div class="col-md-4">
        <x-admin.image-upload name="profile_image" label="Profile Image" :existing="$isEdit ? $testimonial->profile_image : null" />
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-medium d-block">Rating <span class="text-danger">*</span></label>
        <div id="star-picker" class="fs-4">
            @for ($i = 1; $i <= 5; $i++)
                <i class="bi star-icon" data-value="{{ $i }}" style="cursor:pointer;"></i>
            @endfor
        </div>
        <input type="hidden" name="rating" id="rating-input" value="{{ old('rating', $isEdit ? $testimonial->rating : 5) }}">
        @error('rating') <div class="text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <x-admin.select name="status" label="Status" required="true"
            :options="[1 => 'Active', 0 => 'Inactive']"
            :selected="$isEdit ? (int) $testimonial->status : 1" />
    </div>

    <div class="col-12">
        <label class="form-label small fw-medium" for="testimonial">Testimonial <span class="text-danger">*</span></label>
        <textarea name="testimonial" id="testimonial" class="form-control @error('testimonial') is-invalid @enderror" rows="4" maxlength="2000">{{ old('testimonial', $isEdit ? $testimonial->testimonial : '') }}</textarea>
        @error('testimonial') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <x-admin.input name="display_order" label="Display Order" type="number" :value="$isEdit ? $testimonial->display_order : 0" />
    </div>
    <div class="col-md-4 d-flex align-items-center">
        <div class="form-check mt-4">
            <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1"
                {{ old('is_featured', $isEdit ? $testimonial->is_featured : false) ? 'checked' : '' }}>
            <label class="form-check-label small" for="is_featured">Mark as Featured Testimonial</label>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> {{ $isEdit ? 'Update' : 'Save' }}</button>
    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

@push('scripts')
<script>
    (function () {
        const stars = document.querySelectorAll('#star-picker .star-icon');
        const input = document.getElementById('rating-input');

        function paint(value) {
            stars.forEach(star => {
                const active = parseInt(star.dataset.value) <= value;
                star.className = 'star-icon bi ' + (active ? 'bi-star-fill text-warning' : 'bi-star text-muted');
            });
        }

        paint(parseInt(input.value) || 5);

        stars.forEach(star => {
            star.addEventListener('click', function () {
                input.value = this.dataset.value;
                paint(parseInt(this.dataset.value));
            });
        });
    })();
</script>
@endpush
