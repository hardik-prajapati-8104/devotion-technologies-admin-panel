@extends('backend.layouts.master')

@section('title', 'Activity Logs')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Activity Logs</li>
        </ol>
    </nav>
    <h4>Activity Logs</h4>
    <p class="subtitle">A record of every create, update, and delete action taken across the admin panel.</p>
@endsection

@section('admin-content')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small fw-medium">Admin</label>
                <select name="admin" class="form-select">
                    <option value="">All</option>
                    @foreach ($admins as $admin)
                        <option value="{{ $admin->id }}" {{ request('admin') == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-medium">Module</label>
                <select name="module" class="form-select">
                    <option value="">All</option>
                    @foreach ($modules as $module)
                        <option value="{{ $module }}" {{ request('module') === $module ? 'selected' : '' }}>{{ $module }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-medium">Action</label>
                <select name="action" class="form-select">
                    <option value="">All</option>
                    <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                    <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>Logout</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-medium">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-medium">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if ($logs->isEmpty())
            <x-admin.empty-state icon="bi-clock-history" title="No activity found" description="Try adjusting the filters above." />
        @else
            @php
                $actionColors = ['created' => 'success', 'updated' => 'warning', 'deleted' => 'danger', 'login' => 'primary', 'logout' => 'secondary'];
            @endphp
            <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>When</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                    <tr>
                        <td>{{ $log->admin->name ?? 'System' }}</td>
                        <td><span class="badge bg-{{ $actionColors[$log->action] ?? 'secondary' }} text-capitalize">{{ $log->action }}</span></td>
                        <td>{{ $log->module }}</td>
                        <td>{{ $log->description }}</td>
                        <td class="text-muted small">{{ $log->ip_address ?? '—' }}</td>
                        <td class="text-muted small" title="{{ $log->created_at->format('Y-m-d H:i:s') }}">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="mt-3">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
@endsection
