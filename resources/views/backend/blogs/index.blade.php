@extends('backend.layouts.master')

@section('title', 'Blogs')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Blogs</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Blog Management</h4>
            <p class="subtitle">Write, schedule, and publish articles for the Devotion Technology blog.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-outline-secondary"><i class="bi bi-bookmark me-1"></i> Categories</a>
            <a href="{{ route('admin.blog-tags.index') }}" class="btn btn-outline-secondary"><i class="bi bi-tags me-1"></i> Tags</a>
            @can('blogs.create')
                <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Blog</a>
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
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by title...">
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
                    @foreach (\App\Models\Blog::STATUSES as $value => $label)
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
        @if ($blogs->isEmpty())
            <x-admin.empty-state
                icon="bi-journal-richtext"
                title="No blog posts found"
                description="Write your first post or adjust the filters above."
                actionLabel="Add Blog"
                :actionUrl="route('admin.blogs.create')" />
        @else
            <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th width="3%">#</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Publish Date</th>
                        <th>Status</th>
                        <th width="8%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($blogs as $blog)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if ($blog->thumbnail ?? $blog->featured_image)
                                    <img src="{{ url('public/storage/'.($blog->thumbnail ?? $blog->featured_image)) }}" width="36" height="36" class="rounded" style="object-fit:cover;">
                                @endif
                                <div>
                                    {{ $blog->title }}
                                    @if ($blog->is_featured) <i class="bi bi-star-fill text-warning ms-1" title="Featured"></i> @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $blog->category->name ?? '—' }}</td>
                        <td>{{ $blog->author->name ?? '—' }}</td>
                        <td>{{ $blog->publish_date?->format('M d, Y') ?? '—' }}</td>
                        <td>
                            @php $statusColors = ['draft' => 'secondary', 'published' => 'success', 'scheduled' => 'warning']; @endphp
                            <span class="badge bg-{{ $statusColors[$blog->status] ?? 'secondary' }} text-capitalize">{{ $blog->status }}</span>
                        </td>
                        <td>
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">&#x22EE;</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('admin.blogs.preview', $blog->id) }}" target="_blank"><i class="bi bi-eye me-2"></i>Preview</a></li>
                                @can('blogs.edit')
                                    <li><a class="dropdown-item" href="{{ route('admin.blogs.edit', $blog->id) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                @endcan
                                @can('blogs.create')
                                    <li>
                                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('duplicate-form-{{ $blog->id }}').submit();">
                                            <i class="bi bi-copy me-2"></i>Duplicate
                                        </a>
                                        <form id="duplicate-form-{{ $blog->id }}" action="{{ route('admin.blogs.duplicate', $blog->id) }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                @endcan
                                @can('blogs.delete')
                                    <li>
                                        <x-admin.confirm-delete
                                            :action="route('admin.blogs.destroy', $blog->id)"
                                            :form-id="'delete-blog-'.$blog->id"
                                            :label="$blog->title" />
                                    </li>
                                @endcan
                            </ul>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="mt-3">{{ $blogs->links() }}</div>
        @endif
    </div>
</div>
@endsection
