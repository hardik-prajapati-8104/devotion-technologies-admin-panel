@extends('backend.layouts.master')

@section('title', 'Projects')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Projects</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Projects</h4>
            <p class="subtitle">Manage the portfolio shown on the Devotion Technology website.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.project-categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-tags me-1"></i> Categories
            </a>
            @can('projects.create')
                <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Add Project
                </a>
            @endcan
        </div>
    </div>
@endsection

@section('admin-content')

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
                    @foreach (\App\Models\Project::STATUSES as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
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
        @if ($projects->isEmpty())
            <x-admin.empty-state
                icon="bi-kanban"
                title="No projects found"
                description="Add your first project or adjust the filters above."
                actionLabel="Add Project"
                :actionUrl="route('admin.projects.create')" />
        @else
            <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th width="3%">#</th>
                        <th>Project</th>
                        <th>Category</th>
                        <th>Client</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th width="8%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $project)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if ($project->featured_image)
                                    <img src="{{ url('public/storage/'.$project->featured_image) }}" width="36" height="36" class="rounded" style="object-fit:cover;">
                                @endif
                                {{ $project->name }}
                            </div>
                        </td>
                        <td>{{ $project->category->name ?? '—' }}</td>
                        <td>{{ $project->client_name ?? '—' }}</td>
                        <td>
                            @php
                                $statusColors = ['draft' => 'secondary', 'in_progress' => 'warning', 'completed' => 'success', 'archived' => 'dark'];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$project->status] ?? 'secondary' }}">{{ $project->status_label }}</span>
                        </td>
                        <td>{!! $project->is_featured ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-muted"></i>' !!}</td>
                        <td>
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">&#x22EE;</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @can('projects.edit')
                                    <li><a class="dropdown-item" href="{{ route('admin.projects.edit', $project->id) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                @endcan
                                @can('projects.delete')
                                    <li>
                                        <x-admin.confirm-delete
                                            :action="route('admin.projects.destroy', $project->id)"
                                            :form-id="'delete-project-'.$project->id"
                                            :label="$project->name" />
                                    </li>
                                @endcan
                            </ul>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="mt-3">{{ $projects->links() }}</div>
        @endif
    </div>
</div>
@endsection
