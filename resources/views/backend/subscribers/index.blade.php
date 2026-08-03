@extends('backend.layouts.master')

@section('title', 'Newsletter Subscribers')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Newsletter Subscribers</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Newsletter Subscribers</h4>
            <p class="subtitle">Everyone who has signed up for the Devotion Technology newsletter.</p>
        </div>
        <a href="{{ route('admin.subscribers.export') }}" class="btn btn-outline-secondary">
            <i class="bi bi-download me-1"></i> Export CSV
        </a>
    </div>
@endsection

@section('admin-content')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label small fw-medium">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by email or name...">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-medium">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Unsubscribed</option>
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
        @if ($subscribers->isEmpty())
            <x-admin.empty-state icon="bi-newspaper" title="No subscribers yet" description="Subscribers will appear here once visitors sign up through the website." />
        @else
            <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th width="3%">#</th>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Subscribed</th>
                        <th>Status</th>
                        <th width="8%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subscribers as $subscriber)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $subscriber->email }}</td>
                        <td>{{ $subscriber->name ?? '—' }}</td>
                        <td>{{ $subscriber->subscribed_at?->format('M d, Y') }}</td>
                        <td><x-admin.status-badge :status="$subscriber->status" active-label="Active" inactive-label="Unsubscribed" /></td>
                        <td>
                            @can('subscribers.delete')
                                <a href="#" class="text-danger" onclick="event.preventDefault(); confirmDelete('delete-sub-{{ $subscriber->id }}', '{{ $subscriber->email }}');">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <form id="delete-sub-{{ $subscriber->id }}" action="{{ route('admin.subscribers.destroy', $subscriber->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="mt-3">{{ $subscribers->links() }}</div>
        @endif
    </div>
</div>
@endsection
