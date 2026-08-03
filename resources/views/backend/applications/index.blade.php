@extends('backend.layouts.master')

@section('title', 'Career Applications')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Career Applications</li>
        </ol>
    </nav>
    <h4>Career Applications</h4>
    <p class="subtitle">Review candidates who applied through the Careers page.</p>
@endsection

@section('admin-content')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-medium">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by name or email...">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-medium">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach (\App\Models\CareerApplication::STATUSES as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if ($applications->isEmpty())
            <x-admin.empty-state icon="bi-file-earmark-person" title="No applications found" description="Applications will appear here once candidates apply through the website." />
        @else
            <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th width="3%">#</th>
                        <th>Applicant</th>
                        <th>Applied For</th>
                        <th>Applied On</th>
                        <th>Status</th>
                        <th width="8%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($applications as $application)
                    @php
                        $statusColors = ['new' => 'primary', 'reviewing' => 'secondary', 'shortlisted' => 'info', 'interview' => 'warning', 'selected' => 'success', 'rejected' => 'danger'];
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            {{ $application->applicant_name }}
                            <div class="text-muted" style="font-size:11.5px;">{{ $application->email }}</div>
                        </td>
                        <td>{{ $application->career->title ?? '—' }}</td>
                        <td>{{ $application->created_at->format('M d, Y') }}</td>
                        <td><span class="badge bg-{{ $statusColors[$application->status] ?? 'secondary' }}">{{ $application->status_label }}</span></td>
                        <td>
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">&#x22EE;</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('admin.applications.show', $application->id) }}"><i class="bi bi-eye me-2"></i>View</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.applications.resume', $application->id) }}"><i class="bi bi-download me-2"></i>Download Resume</a></li>
                                @can('applications.delete')
                                    <li>
                                        <x-admin.confirm-delete
                                            :action="route('admin.applications.destroy', $application->id)"
                                            :form-id="'delete-app-'.$application->id"
                                            :label="'the application from '.$application->applicant_name" />
                                    </li>
                                @endcan
                            </ul>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="mt-3">{{ $applications->links() }}</div>
        @endif
    </div>
</div>
@endsection
