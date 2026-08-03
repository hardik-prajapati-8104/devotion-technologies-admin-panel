@extends('backend.layouts.master')

@section('title', 'Careers')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Careers</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Career Openings</h4>
            <p class="subtitle">Manage job listings shown on the Careers page.</p>
        </div>
        @can('careers.create')
            <a href="{{ route('admin.careers.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Job Listing</a>
        @endcan
    </div>
@endsection

@section('admin-content')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label small fw-medium">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by title...">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-medium">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Open</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Closed</option>
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
        @if ($careers->isEmpty())
            <x-admin.empty-state
                icon="bi-briefcase"
                title="No job listings found"
                description="Post your first opening or adjust the filters above."
                actionLabel="Add Job Listing"
                :actionUrl="route('admin.careers.create')" />
        @else
            <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th width="3%">#</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Deadline</th>
                        <th>Applications</th>
                        <th>Status</th>
                        <th width="8%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($careers as $career)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            {{ $career->title }}
                            @if ($career->is_featured) <i class="bi bi-star-fill text-warning ms-1"></i> @endif
                        </td>
                        <td>{{ $career->employment_type_label }}</td>
                        <td>{{ $career->location ?? '—' }}</td>
                        <td>
                            {{ $career->application_deadline?->format('M d, Y') ?? 'No deadline' }}
                            @if ($career->is_expired) <span class="badge bg-secondary">Expired</span> @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.applications.index', ['career' => $career->id]) }}">{{ $career->applications_count }}</a>
                        </td>
                        <td><x-admin.status-badge :status="$career->status" active-label="Open" inactive-label="Closed" /></td>
                        <td>
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">&#x22EE;</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @can('careers.edit')
                                    <li><a class="dropdown-item" href="{{ route('admin.careers.edit', $career->id) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                @endcan
                                @can('careers.delete')
                                    <li>
                                        <x-admin.confirm-delete
                                            :action="route('admin.careers.destroy', $career->id)"
                                            :form-id="'delete-career-'.$career->id"
                                            :label="$career->title" />
                                    </li>
                                @endcan
                            </ul>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="mt-3">{{ $careers->links() }}</div>
        @endif
    </div>
</div>
@endsection
