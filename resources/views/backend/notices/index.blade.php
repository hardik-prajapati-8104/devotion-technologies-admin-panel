@extends('backend.layouts.master')

@section('title', 'Notices')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Notices</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Notices</h4>
            <p class="subtitle">Targeted notices aimed at specific roles or teams.</p>
        </div>
        @can('notices.create')
            <a href="{{ route('admin.notices.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> New Notice
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
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by title...">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-medium">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
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
        @if ($notices->isEmpty())
            <x-admin.empty-state
                icon="bi-bell"
                title="No notices yet"
                description="Use the New Notice button above to send a targeted notice to a specific team." />
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Audience</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Published</th>
                            <th class="text-end" style="width:120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notices as $n)
                            <tr>
                                <td class="fw-medium">{{ $n->title }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $n->audienceLabel() }}</span></td>
                                <td>{{ $n->author->name ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $n->status === 'published' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($n->status) }}</span>
                                </td>
                                <td>{{ $n->published_at?->format('d M Y') ?? '—' }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        @can('notices.edit')
                                            <a href="{{ route('admin.notices.edit', $n->id) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('notices.delete')
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('delete-notice-{{ $n->id }}', '{{ $n->title }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="delete-notice-{{ $n->id }}" action="{{ route('admin.notices.destroy', $n->id) }}" method="POST" class="d-none">
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

            <div class="mt-3">{{ $notices->links() }}</div>
        @endif
    </div>
</div>

@endsection
