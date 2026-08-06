@extends('backend.layouts.master')

@section('title', 'Login Attempts')

@section('admin-content')
    <style>
        .table td,
        .table th{
            vertical-align: middle;
            white-space: nowrap;
        }

        .table-hover tbody tr:hover{
            background:#f8fafc;
        }

        .badge{
            font-size:12px;
            font-weight:600;
            border-radius:6px;
        }

        .card{
            border-radius:12px;
        }

        .form-control,
        .form-select{
            height:42px;
        }

        .btn-primary{
            height:42px;
        }
    </style>
<div class="card shadow-sm border-0">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-shield-alt me-2"></i>Login Attempts
        </h5>
    </div>

    <div class="card-body">

        <form method="GET" class="row g-3 align-items-end mb-4">

            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-semibold">Email</label>
                <input
                    type="text"
                    name="email"
                    class="form-control"
                    placeholder="Search email..."
                    value="{{ request('email') }}">
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-semibold">IP Address</label>
                <input
                    type="text"
                    name="ip"
                    class="form-control"
                    placeholder="127.0.0.1"
                    value="{{ request('ip') }}">
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-semibold">Status</label>

                <select name="status" class="form-select">
                    <option value="">Any Status</option>

                    <option value="success"
                        {{ request('status')=='success' ? 'selected' : '' }}>
                        Successful
                    </option>

                    <option value="failed"
                        {{ request('status')=='failed' ? 'selected' : '' }}>
                        Failed
                    </option>
                </select>

            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-semibold">From</label>

                <input
                    type="date"
                    name="from"
                    class="form-control"
                    value="{{ request('from') }}">
            </div>

            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-semibold">To</label>

                <input
                    type="date"
                    name="to"
                    class="form-control"
                    value="{{ request('to') }}">
            </div>

            <div class="col-lg-1 col-md-6 d-grid">
                <button class="btn btn-primary">
                    Filter
                </button>
            </div>

        </form>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>
                        <th style="width:180px;">Time</th>
                        <th>Email</th>
                        <th style="width:120px;">IP</th>
                        <th style="width:110px;">Status</th>
                        <th style="width:180px;">Reason</th>
                        <th>User Agent</th>
                    </tr>

                </thead>

                <tbody>

                @forelse($attempts as $a)

                    <tr>

                        <td>
                            {{ $a->created_at
                                ->timezone('Asia/Kolkata')
                                ->format('d M Y, h:i A') }}
                        </td>

                        <td class="fw-medium">
                            {{ $a->email }}
                        </td>

                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $a->ip }}
                            </span>
                        </td>

                        <td>

                            @if($a->successful)

                                <span class="badge bg-success px-3 py-2">
                                    Success
                                </span>

                            @else

                                <span class="badge bg-danger px-3 py-2">
                                    Failed
                                </span>

                            @endif

                        </td>

                        <td>

                            @if($a->reason)

                                <span class="text-muted">
                                    {{ str_replace('_',' ', ucfirst($a->reason)) }}
                                </span>

                            @else

                                —

                            @endif

                        </td>

                        <td>

                            <span
                                title="{{ $a->user_agent }}"
                                class="d-inline-block text-truncate"
                                style="max-width:350px;">

                                {{ $a->user_agent }}

                            </span>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="text-center py-5 text-muted">

                            No login attempts found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>
 
                <div class="card-footer bg-white">

                    {{ $attempts->withQueryString()->links('pagination::bootstrap-5') }}

                </div> 

    </div>
</div>

@endsection