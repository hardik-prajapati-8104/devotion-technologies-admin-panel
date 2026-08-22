@extends('backend.layouts.master')

@section('title', 'SEO Management')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">SEO Management</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h4>SEO Management</h4>
            <p class="subtitle">Meta tags for every page on the site. System pages (Home, About Us, etc.) are always available here and can't be removed. Add a custom page for anything else — just give it a label and its front-end URL, no code changes needed.</p>
        </div>
        @can('seo.create')
            <a href="{{ route('admin.seo.create') }}" class="btn btn-primary flex-shrink-0">
                <i class="bi bi-plus-lg me-1"></i> Add New Page
            </a>
        @endcan
    </div>
@endsection

@section('admin-content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>URL</th>
                    <th>SEO Title</th>
                    <th>Meta Description</th>
                    <th width="10%">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pages as $page)
                <tr>
                    <td class="fw-medium">
                        {{ $page->page_label }}
                        @if ($page->is_default)
                            <span class="badge bg-secondary ms-1">System Page</span>
                        @else
                            <span class="badge bg-info text-dark ms-1">Custom</span>
                        @endif
                    </td>
                    <td><code>{{ $page->page_url }}</code></td>
                    <td>{{ $page->seo_title ?: '—' }}</td>
                    <td class="text-truncate" style="max-width: 320px;">{{ $page->meta_description ?: '—' }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            @can('seo.edit')
                                <a href="{{ route('admin.seo.edit', $page->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            @endcan
                            @can('seo.delete')
                                @unless ($page->is_default)
                                    <form action="{{ route('admin.seo.destroy', $page->id) }}" method="POST"
                                          onsubmit="return confirm('Delete SEO settings for &quot;{{ $page->page_label }}&quot;? This can\'t be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endunless
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No pages yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
