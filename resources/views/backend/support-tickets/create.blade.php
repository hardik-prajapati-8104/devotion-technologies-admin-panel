@extends('backend.layouts.master')

@section('title', 'New Support Ticket')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.support-tickets.index') }}">Support Tickets</a></li>
            <li class="breadcrumb-item active">New Ticket</li>
        </ol>
    </nav>
    <div>
        <h4>New Support Ticket</h4>
        <p class="subtitle">Log a request from a staff member or an external contact.</p>
    </div>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.support-tickets.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label small fw-medium">Subject <span class="text-danger">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}"
                           class="form-control @error('subject') is-invalid @enderror" placeholder="Short summary of the issue" required>
                    @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label small fw-medium">Description <span class="text-danger">*</span></label>
                    <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-medium">Priority <span class="text-danger">*</span></label>
                    <select name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                        @foreach (['low', 'medium', 'high', 'urgent'] as $p)
                            <option value="{{ $p }}" {{ old('priority', 'medium') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                    @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-medium">Category</label>
                    <input type="text" name="category" value="{{ old('category') }}"
                           class="form-control @error('category') is-invalid @enderror" placeholder="e.g. Billing, Technical, Access">
                    @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-medium">Assign To</label>
                    <select name="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror">
                        <option value="">Unassigned</option>
                        @foreach ($admins as $a)
                            <option value="{{ $a->id }}" {{ old('assigned_to') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                        @endforeach
                    </select>
                    @error('assigned_to') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label small fw-medium d-block">Requester</label>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="requester_type" id="reqInternal" value="internal" {{ old('requester_type', 'internal') === 'internal' ? 'checked' : '' }}>
                        <label class="btn btn-outline-secondary btn-sm" for="reqInternal">Internal (Admin)</label>

                        <input type="radio" class="btn-check" name="requester_type" id="reqExternal" value="external" {{ old('requester_type') === 'external' ? 'checked' : '' }}>
                        <label class="btn btn-outline-secondary btn-sm" for="reqExternal">External Contact</label>
                    </div>
                </div>

                <div class="col-md-6" id="internalRequesterField">
                    <label class="form-label small fw-medium">Requesting Admin</label>
                    <select name="requester_admin_id" class="form-select @error('requester_admin_id') is-invalid @enderror">
                        <option value="">Select admin...</option>
                        @foreach ($admins as $a)
                            <option value="{{ $a->id }}" {{ old('requester_admin_id') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                        @endforeach
                    </select>
                    @error('requester_admin_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3 d-none" id="externalNameField">
                    <label class="form-label small fw-medium">Contact Name</label>
                    <input type="text" name="requester_name" value="{{ old('requester_name') }}" class="form-control @error('requester_name') is-invalid @enderror">
                    @error('requester_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3 d-none" id="externalEmailField">
                    <label class="form-label small fw-medium">Contact Email</label>
                    <input type="email" name="requester_email" value="{{ old('requester_email') }}" class="form-control @error('requester_email') is-invalid @enderror">
                    @error('requester_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.support-tickets.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Create Ticket</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleRequesterFields() {
        const internal = document.getElementById('reqInternal').checked;
        document.getElementById('internalRequesterField').classList.toggle('d-none', !internal);
        document.getElementById('externalNameField').classList.toggle('d-none', internal);
        document.getElementById('externalEmailField').classList.toggle('d-none', internal);
    }
    document.getElementById('reqInternal').addEventListener('change', toggleRequesterFields);
    document.getElementById('reqExternal').addEventListener('change', toggleRequesterFields);
    toggleRequesterFields();
</script>
@endsection
