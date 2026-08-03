@extends('backend.layouts.master')

@section('title', 'Enquiry — '.$enquiry->name)

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.enquiries.index') }}">Contact Enquiries</a></li>
            <li class="breadcrumb-item active">{{ $enquiry->name }}</li>
        </ol>
    </nav>
    <h4>{{ $enquiry->subject ?: 'Enquiry from '.$enquiry->name }}</h4>
    <p class="subtitle">Received {{ $enquiry->created_at->format('F d, Y \a\t g:i A') }}</p>
@endsection

@section('admin-content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Message</h6>
                <p class="mb-0" style="white-space: pre-line;">{{ $enquiry->message }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Contact Details</h6>
                <p class="small mb-1"><i class="bi bi-person me-2 text-muted"></i>{{ $enquiry->name }}</p>
                <p class="small mb-1"><i class="bi bi-envelope me-2 text-muted"></i>{{ $enquiry->email }}</p>
                <p class="small mb-1"><i class="bi bi-telephone me-2 text-muted"></i>{{ $enquiry->phone ?? '—' }}</p>
                <p class="small mb-0"><i class="bi bi-signpost me-2 text-muted"></i>{{ $enquiry->source ?? 'Contact Page' }}</p>

                <a href="mailto:{{ $enquiry->email }}?subject=Re: {{ $enquiry->subject ?? 'Your enquiry' }}" class="btn btn-outline-primary btn-sm w-100 mt-3">
                    <i class="bi bi-reply me-1"></i> Reply by Email
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Enquiry Status</h6>
                <form action="{{ route('admin.enquiries.update-status', $enquiry->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-select mb-2" onchange="this.form.submit()">
                        @foreach (\App\Models\ContactEnquiry::STATUSES as $value => $label)
                            <option value="{{ $value }}" {{ $enquiry->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <noscript><button type="submit" class="btn btn-primary btn-sm w-100">Update Status</button></noscript>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
