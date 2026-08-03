@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'help' => null])

<div>
    <label class="form-label small fw-medium" for="{{ $name }}">
        {{ $label }} @if ($required) <span class="text-danger">*</span> @endif
    </label>
    <input type="{{ $type }}" class="form-control @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}"
           value="{{ old($name, $value) }}" {{ $attributes }}>
    @error($name) <div class="invalid-feedback">{{ $message }}</div> @enderror
    @if ($help) <div class="form-text">{{ $help }}</div> @endif
</div>
