@extends('backend.layouts.master')

@section('title', 'Our Clients')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Our Clients</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Our Clients</h4>
            <p class="subtitle">Manage the client logos and details shown across the panel and public site.</p>
        </div>
        @can('clients.create')
            <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Client
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
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by client or company name...">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-medium">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body">
        @if ($clients->isEmpty())
            <x-admin.empty-state
                icon="bi-people"
                title="No clients added yet"
                description="Use the Add Client button above to start building your client showcase." />
        @else
            <form action="{{ route('admin.clients.bulk-destroy') }}" method="POST" id="bulkDeleteForm">
                @csrf
                @method('DELETE')

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th style="width:30px;"><input type="checkbox" id="selectAll"></th>
                                <th style="width:56px;">Logo</th>
                                <th>Client Name</th>
                                <th>Company</th>
                                <th>Website</th>
                                <th>Contact</th>
                                <th style="width:80px;">Sort</th>
                                <th style="width:100px;">Status</th>
                                <th class="text-end" style="width:140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="{{ $client->id }}" class="row-checkbox"></td>
                                    <td>
                                        @if ($client->company_logo)
                                            <img src="{{ url('storage/app/public/'.$client->company_logo) }}" alt="{{ $client->company_name }}" class="rounded" style="width:32px; height:32px; object-fit:contain;">
                                        @else
                                            <span class="text-muted"><i class="bi bi-building"></i></span>
                                        @endif
                                    </td>
                                    <td class="fw-medium">{{ $client->client_name }}</td>
                                    <td>{{ $client->company_name }}</td>
                                    <td>
                                        @if ($client->company_website)
                                            <a href="{{ $client->company_website }}" target="_blank" rel="noopener">
                                                {{ \Illuminate\Support\Str::limit($client->company_website, 30) }}
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $client->company_contact ?: '—' }}</td>
                                    <td>{{ $client->sort_order }}</td>
                                    <td>
                                        @can('clients.edit')
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input status-toggle" type="checkbox"
                                                       data-id="{{ $client->id }}"
                                                       {{ $client->status ? 'checked' : '' }}>
                                            </div>
                                        @else
                                            <span class="badge {{ $client->status ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $client->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        @endcan
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            @can('clients.edit')
                                                <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endcan
                                            @can('clients.delete')
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('delete-client-{{ $client->id }}', '{{ $client->client_name }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @can('clients.delete')
                    <button type="submit" class="btn btn-sm btn-outline-danger" id="bulkDeleteBtn" disabled
                            onclick="return confirm('Delete selected clients? This cannot be undone.');">
                        <i class="bi bi-trash me-1"></i>Delete Selected
                    </button>
                @endcan
            </form>

            {{-- Individual delete forms live outside bulkDeleteForm — HTML doesn't allow nested <form> elements --}}
            @can('clients.delete')
                @foreach ($clients as $client)
                    <form id="delete-client-{{ $client->id }}" action="{{ route('admin.clients.destroy', $client->id) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                @endforeach
            @endcan

            <div class="mt-3">{{ $clients->links() }}</div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
    // AJAX status toggle
    document.querySelectorAll('.status-toggle').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            const id = this.dataset.id;
            const checkbox = this;
            const previous = !checkbox.checked;

            fetch(`{{ url('admin/clients') }}/${id}/toggle-status`, {
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

    // Bulk-delete select-all + enable/disable button
    const selectAll = document.getElementById('selectAll');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

    function toggleBulkButton() {
        const anyChecked = document.querySelectorAll('.row-checkbox:checked').length > 0;
        if (bulkDeleteBtn) bulkDeleteBtn.disabled = !anyChecked;
    }

    selectAll?.addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
        toggleBulkButton();
    });

    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.addEventListener('change', toggleBulkButton);
    });
</script>
@endsection