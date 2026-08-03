@extends('backend.layouts.master')

@section('title', 'Application — '.$application->applicant_name)

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.applications.index') }}">Career Applications</a></li>
            <li class="breadcrumb-item active">{{ $application->applicant_name }}</li>
        </ol>
    </nav>
    <h4>{{ $application->applicant_name }}</h4>
    <p class="subtitle">Applied for {{ $application->career->title ?? 'a general position' }} on {{ $application->created_at->format('F d, Y') }}</p>
@endsection

@section('admin-content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Cover Letter</h6>
                <p class="mb-0" style="white-space: pre-line;">{{ $application->cover_letter ?: 'No cover letter was submitted.' }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Contact Details</h6>
                <p class="small mb-1"><i class="bi bi-envelope me-2 text-muted"></i>{{ $application->email }}</p>
                <p class="small mb-1"><i class="bi bi-telephone me-2 text-muted"></i>{{ $application->phone ?? '—' }}</p>
                <p class="small mb-0"><i class="bi bi-briefcase me-2 text-muted"></i>{{ $application->career->title ?? 'General Application' }}</p>

                <a href="{{ route('admin.applications.resume', $application->id) }}" class="btn btn-outline-primary btn-sm w-100 mt-3">
                    <i class="bi bi-download me-1"></i> Download Resume
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Application Status</h6>
                <form action="{{ route('admin.applications.update-status', $application->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-select mb-2" onchange="this.form.submit()">
                        @foreach (\App\Models\CareerApplication::STATUSES as $value => $label)
                            <option value="{{ $value }}" {{ $application->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <noscript><button type="submit" class="btn btn-primary btn-sm w-100">Update Status</button></noscript>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
