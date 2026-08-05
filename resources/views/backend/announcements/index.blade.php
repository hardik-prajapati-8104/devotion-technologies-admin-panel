@extends('backend.layouts.master')

@section('title', 'Announcements')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Announcements</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Announcements</h4>
            <p class="subtitle">Org-wide announcements visible to everyone in the panel.</p>
        </div>
        @can('announcements.create')
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> New Announcement
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
        @if ($announcements->isEmpty())
            <x-admin.empty-state
                icon="bi-megaphone"
                title="No announcements yet"
                description="Use the New Announcement button above to broadcast something to the whole panel." />
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Published</th>
                            <th class="text-end" style="width:120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($announcements as $a)
                            <tr>
                                <td>
                                    @if ($a->is_pinned)
                                        <i class="bi bi-pin-angle-fill text-warning me-1" title="Pinned"></i>
                                    @endif
                                    <span class="fw-medium">{{ $a->title }}</span>
                                </td>
                                <td>{{ $a->author->name ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $a->status === 'published' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($a->status) }}</span>
                                </td>
                                <td>{{ $a->published_at?->format('d M Y') ?? '—' }}</td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        @can('announcements.edit')
                                            <a href="{{ route('admin.announcements.edit', $a->id) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('announcements.delete')
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('delete-announcement-{{ $a->id }}', '{{ $a->title }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="delete-announcement-{{ $a->id }}" action="{{ route('admin.announcements.destroy', $a->id) }}" method="POST" class="d-none">
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

            <div class="mt-3">{{ $announcements->links() }}</div>
        @endif
    </div>
</div>

@endsection
