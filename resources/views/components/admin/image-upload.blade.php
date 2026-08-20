@props(['name', 'label' => 'Image', 'existing' => null, 'required' => false, 'help' => null])

<div>
    <label class="form-label small fw-medium" for="{{ $name }}">
        {{ $label }} @if ($required) <span class="text-danger">*</span> @endif
    </label>
    <input type="file" class="form-control @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}"
           accept="image/png,image/jpeg,image/webp">
    @error($name) <div class="invalid-feedback">{{ $message }}</div> @enderror
    @if ($help) <div class="form-text">{{ $help }}</div> @endif

    @if ($existing)
        <div class="mt-2">
            <img src="{{ url('storage/app/public/'.$existing) }}" class="rounded border" width="300px;" height="auto" style="object-fit:fill;">
        </div>
    @endif
</div>
