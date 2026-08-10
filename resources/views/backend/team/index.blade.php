@extends('backend.layouts.master')

@section('title', 'Team Management')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Team Management</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4>Team Management</h4>
            <p class="subtitle">Manage the team members shown on the About Us page.</p>
        </div>
        @can('team.create')
            <a href="{{ route('admin.team.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Team Member
            </a>
        @endcan
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
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    @forelse ($members as $member)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <img src="{{ $member->profile_image ? url('storage/app/public/'.$member->profile_image) : 'https://ui-avatars.com/api/?background=aa8038&color=fff&size=96&name='.urlencode($member->name) }}"
                         width="72" height="72" class="rounded-circle mb-2" style="object-fit:cover;">
                    <h6 class="fw-semibold mb-0">{{ $member->name }}</h6>
                    <p class="text-muted small mb-2">{{ $member->designation }}{{ $member->department ? ' · '.$member->department : '' }}</p>
                    <x-admin.status-badge :status="$member->status" />

                    <div class="d-flex justify-content-center gap-2 mt-3">
                        @can('team.edit')
                            <a href="{{ route('admin.team.edit', $member->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        @endcan
                        @can('team.delete')
                            <a href="#" class="btn btn-sm btn-outline-danger" onclick="event.preventDefault(); confirmDelete('delete-member-{{ $member->id }}', '{{ $member->name }}');">
                                <i class="bi bi-trash"></i>
                            </a>
                            <form id="delete-member-{{ $member->id }}" action="{{ route('admin.team.destroy', $member->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <x-admin.empty-state
                        icon="bi-people"
                        title="No team members yet"
                        description="Add your first team member."
                        actionLabel="Add Team Member"
                        :actionUrl="route('admin.team.create')" />
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-3">{{ $members->links() }}</div>
@endsection
