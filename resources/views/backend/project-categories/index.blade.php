@extends('backend.layouts.master')

@section('title', 'Project Categories')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
            <li class="breadcrumb-item active">Categories</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4>Project Categories</h4>
            <p class="subtitle">Group your portfolio projects for easier browsing.</p>
        </div>
        @can('projects.create')
            <a href="{{ route('admin.project-categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Category
            </a>
        @endcan
    </div>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        @if ($categories->isEmpty())
            <x-admin.empty-state
                icon="bi-tags"
                title="No categories yet"
                description="Create your first project category to start organizing projects."
                actionLabel="Add Category"
                :actionUrl="route('admin.project-categories.create')" />
        @else
            <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th width="3%">#</th>
                        <th>Name</th>
                        <th>Projects</th>
                        <th>Status</th>
                        <th width="8%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                    <tr>
                        <td class="text-center">{{ $loop->index + 1 }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->projects_count }}</td>
                        <td><x-admin.status-badge :status="$category->status" /></td>
                        <td>
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">&#x22EE;</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @can('projects.edit')
                                    <li><a class="dropdown-item" href="{{ route('admin.project-categories.edit', $category->id) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                @endcan
                                @can('projects.delete')
                                    <li>
                                        <x-admin.confirm-delete
                                            :action="route('admin.project-categories.destroy', $category->id)"
                                            :form-id="'delete-pcat-'.$category->id"
                                            :label="$category->name" />
                                    </li>
                                @endcan
                            </ul>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        @endif
    </div>
</div>
@endsection
