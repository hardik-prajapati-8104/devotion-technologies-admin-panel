@extends('backend.layouts.master')

@section('title', 'Services')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Services</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Services</h4>
            <p class="subtitle">Manage the services shown on the Devotion Technology website.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.service-categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-tags me-1"></i> Categories
            </a>
            @can('services.create')
                <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Add Service
                </a>
            @endcan
        </div>
    </div>
@endsection

@section('admin-content')

<!-- Filters -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-medium">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by name...">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-medium">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-medium">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Published</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Unpublished</option>
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
        @if ($services->isEmpty())
            <x-admin.empty-state
                icon="bi-briefcase"
                title="No services found"
                description="Add your first service or adjust the filters above."
                actionLabel="Add Service"
                :actionUrl="route('admin.services.create')" />
        @else
        <form id="bulk-form" method="POST" action="{{ route('admin.services.bulk-delete') }}">
            @csrf
            @method('DELETE')

            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="selectAll">
                    <label class="form-check-label small" for="selectAll">Select all</label>
                </div>
                @can('services.delete')
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="bulkDeleteConfirm()">
                        <i class="bi bi-trash me-1"></i> Delete Selected
                    </button>
                @endcan
            </div>

            <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th width="2%"></th>
                        <th width="3%">#</th>
                        <th>Service</th>
                        <th>Category</th>
                        <th>Order</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th width="8%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $service)
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $service->id }}" class="form-check-input row-checkbox"></td>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if ($service->featured_image)
                                    <img src="{{ url('public/storage/'.$service->featured_image) }}" width="36" height="36" class="rounded" style="object-fit:cover;">
                                @elseif ($service->icon)
                                    <i class="bi {{ $service->icon }}"></i>
                                @endif
                                {{ $service->name }}
                            </div>
                        </td>
                        <td>{{ $service->category->name ?? '—' }}</td>
                        <td>{{ $service->display_order }}</td>
                        <td>{!! $service->is_featured ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-muted"></i>' !!}</td>
                        <td><x-admin.status-badge :status="$service->status" active-label="Published" inactive-label="Unpublished" /></td>
                        <td>
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">&#x22EE;</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @can('services.edit')
                                    <li><a class="dropdown-item" href="{{ route('admin.services.edit', $service->id) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                @endcan
                                @can('services.delete')
                                    <li>
                                        <x-admin.confirm-delete
                                            :action="route('admin.services.destroy', $service->id)"
                                            :form-id="'delete-service-'.$service->id"
                                            :label="$service->name" />
                                    </li>
                                @endcan
                            </ul>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </form>

        <div class="mt-3">{{ $services->links() }}</div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('selectAll')?.addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
    });

    function bulkDeleteConfirm() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        if (checked.length === 0) {
            Swal.fire('No services selected', 'Please select at least one service to delete.', 'info');
            return;
        }
        Swal.fire({
            title: 'Are you sure?',
            text: `This will permanently remove ${checked.length} service(s). This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#aa8038',
            confirmButtonText: 'Yes, delete them'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('bulk-form').submit();
        });
    }
</script>
@endsection
