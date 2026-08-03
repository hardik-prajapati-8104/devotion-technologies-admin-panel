@extends('backend.layouts.master')

@section('title', 'Blog Categories')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Blogs</a></li>
            <li class="breadcrumb-item active">Categories</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4>Blog Categories</h4>
            <p class="subtitle">Group posts for easier browsing and filtering.</p>
        </div>
        @can('blogs.create')
            <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary">
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
                icon="bi-journal-bookmark"
                title="No categories yet"
                description="Create your first blog category."
                actionLabel="Add Category"
                :actionUrl="route('admin.blog-categories.create')" />
        @else
            <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th width="3%">#</th>
                        <th>Name</th>
                        <th>Blogs</th>
                        <th>Status</th>
                        <th width="8%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                    <tr>
                        <td class="text-center">{{ $loop->index + 1 }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->blogs_count }}</td>
                        <td><x-admin.status-badge :status="$category->status" /></td>
                        <td>
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">&#x22EE;</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @can('blogs.edit')
                                    <li><a class="dropdown-item" href="{{ route('admin.blog-categories.edit', $category->id) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                @endcan
                                @can('blogs.delete')
                                    <li>
                                        <x-admin.confirm-delete
                                            :action="route('admin.blog-categories.destroy', $category->id)"
                                            :form-id="'delete-bcat-'.$category->id"
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
