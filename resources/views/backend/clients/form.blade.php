{{-- Shared fields for create.blade.php and edit.blade.php --}}

@csrf
@isset($client)
    @method('PUT')
@endisset

<div class="row">

    {{-- Client Name --}}
    <div class="col-md-6 mb-3">
        <label class="form-label small fw-medium">Client Name</label>
        <input type="text" name="client_name" placeholder="Enter Client Name"
               class="form-control @error('client_name') is-invalid @enderror"
               value="{{ old('client_name', $client->client_name ?? '') }}" required>
        @error('client_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Company Name --}}
    <div class="col-md-6 mb-3">
        <label class="form-label small fw-medium">Company Name</label>
        <input type="text" name="company_name" placeholder="Enter Company Name"
               class="form-control @error('company_name') is-invalid @enderror"
               value="{{ old('company_name', $client->company_name ?? '') }}" required>
        @error('company_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Company Website --}}
    <div class="col-md-6 mb-3">
        <label class="form-label small fw-medium">Company Website</label>
        <input type="url" name="company_website" placeholder="https://example.com"
               class="form-control @error('company_website') is-invalid @enderror"
               value="{{ old('company_website', $client->company_website ?? '') }}">
        @error('company_website')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Company Email --}}
    <div class="col-md-6 mb-3">
        <label class="form-label small fw-medium">Company Email</label>
        <input type="email" name="company_email" placeholder="contact@company.com"
               class="form-control @error('company_email') is-invalid @enderror"
               value="{{ old('company_email', $client->company_email ?? '') }}">
        @error('company_email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Company Contact --}}
    <div class="col-md-6 mb-3">
        <label class="form-label small fw-medium">Company Contact</label>
        <input type="text" name="company_contact" placeholder="Enter Phone Number"
               class="form-control @error('company_contact') is-invalid @enderror"
               value="{{ old('company_contact', $client->company_contact ?? '') }}">
        @error('company_contact')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Sort Order --}}
    <div class="col-md-6 mb-3">
        <label class="form-label small fw-medium">Sort Order</label>
        <input type="number" name="sort_order" min="0" placeholder="0"
               class="form-control @error('sort_order') is-invalid @enderror"
               value="{{ old('sort_order', $client->sort_order ?? 0) }}">
        @error('sort_order')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Company Logo --}}
    <div class="col-md-6 mb-3">
        <label class="form-label small fw-medium">Company Logo</label>
        <input type="file" name="company_logo" accept="image/*"
               class="form-control @error('company_logo') is-invalid @enderror">
        @error('company_logo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @isset($client)
            @if ($client->company_logo)
                <div class="mt-2">
                    <img src="{{ url('storage/app/public/'.$client->company_logo) }}" alt="{{ $client->company_name }}"
                         style="height:60px; object-fit:contain;" class="border rounded p-1">
                </div>
            @endif
        @endisset
    </div>

    {{-- Status --}}
    <div class="col-md-6 mb-3">
        <label class="form-label small fw-medium d-block">Status</label>
        <div class="form-check form-switch">
            <input type="hidden" name="status" value="0">
            <input class="form-check-input" type="checkbox" role="switch" name="status" value="1"
                   id="statusSwitch"
                   {{ old('status', $client->status ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="statusSwitch">Active</label>
        </div>
    </div>

</div>