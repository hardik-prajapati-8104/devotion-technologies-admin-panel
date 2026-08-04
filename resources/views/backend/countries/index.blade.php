@extends('backend.layouts.master')

@section('title', 'Countries')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Countries</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Countries</h4>
            <p class="subtitle">Manage the countries available across the panel and public site.</p>
        </div>
        @can('countries.create')
            <a href="{{ route('admin.countries.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Country
            </a>
        @endcan
    </div>
@endsection

@section('admin-content')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label small fw-medium">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by name or code...">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-medium">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if ($countries->isEmpty())
            <x-admin.empty-state
                icon="bi-globe-americas"
                title="No countries added yet"
                description="Use the Add Country button above to build the country list for the rest of the panel." />
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="width:56px;">Flag</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Phone Code</th>
                            <th>Status</th>
                            <th class="text-end" style="width:140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($countries as $country)
                            <tr>
                                <td>
                                    @if ($country->flag)
                                        <img src="{{ url('storage/app/public/'.$country->flag) }}" alt="{{ $country->name }}" class="rounded" style="width:32px; height:22px; object-fit:cover;">
                                    @else
                                        <span class="text-muted"><i class="bi bi-flag"></i></span>
                                    @endif
                                </td>
                                <td class="fw-medium">{{ $country->name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $country->code }}</span></td>
                                <td>{{ $country->phone_code ?? '—' }}</td>
                                <td>
                                    @can('countries.edit')
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input status-toggle" type="checkbox"
                                                   data-id="{{ $country->id }}"
                                                   {{ $country->status ? 'checked' : '' }}>
                                        </div>
                                    @else
                                        <span class="badge {{ $country->status ? 'bg-success' : 'bg-secondary' }}">{{ $country->status_label }}</span>
                                    @endcan
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        @can('countries.edit')
                                            <a href="{{ route('admin.countries.edit', $country->id) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('countries.delete')
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('delete-country-{{ $country->id }}', '{{ $country->name }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="delete-country-{{ $country->id }}" action="{{ route('admin.countries.destroy', $country->id) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $countries->links() }}</div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.querySelectorAll('.status-toggle').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            const id = this.dataset.id;
            const checkbox = this;
            const previous = !checkbox.checked;

            fetch(`{{ url('admin/countries') }}/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-HTTP-Method-Override': 'PUT'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) {
                        checkbox.checked = previous;
                    }
                })
                .catch(() => { checkbox.checked = previous; });
        });
    });
</script>
@endsection