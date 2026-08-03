@props(['name', 'label', 'options' => [], 'selected' => null, 'required' => false, 'placeholder' => null])

<div>
    <label class="form-label small fw-medium" for="{{ $name }}">
        {{ $label }} @if ($required) <span class="text-danger">*</span> @endif
    </label>
    <select class="form-select @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" {{ $attributes }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $value => $labelText)
            <option value="{{ $value }}" {{ (string) old($name, $selected) === (string) $value ? 'selected' : '' }}>
                {{ $labelText }}
            </option>
        @endforeach
    </select>
    @error($name) <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
