@extends('backend.layouts.master')

@section('title', 'Support Tickets')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Support Tickets</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Support Tickets</h4>
            <p class="subtitle">Track and resolve support requests from staff and clients.</p>
        </div>
        @can('support-tickets.create')
            <a href="{{ route('admin.support-tickets.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> New Ticket
            </a>
        @endcan
    </div>
@endsection

@section('admin-content')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" id="ticketFilterForm" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-medium">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Ticket # or subject...">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-medium">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    @foreach (['open', 'in_progress', 'resolved', 'closed'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-medium">Priority</label>
                <select name="priority" class="form-select">
                    <option value="">All Priorities</option>
                    @foreach (['low', 'medium', 'high', 'urgent'] as $p)
                        <option value="{{ $p }}" {{ request('priority') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input type="checkbox" name="mine" value="1" class="form-check-input" id="mineCheck"
                           {{ request('mine') ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="form-check-label small" for="mineCheck">Assigned to me only</label>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if ($tickets->isEmpty())
            <x-admin.empty-state
                icon="bi-life-preserver"
                title="No support tickets yet"
                description="Use the New Ticket button above to log a support request." />
        @else
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Ticket #</th>
                            <th>Subject</th>
                            <th>Requester</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th class="text-end" style="width:80px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tickets as $t)
                            <tr>
                                <td><a href="{{ route('admin.support-tickets.show', $t->id) }}" class="fw-medium text-decoration-none">{{ $t->ticket_number }}</a></td>
                                <td class="text-truncate" style="max-width:260px;">{{ $t->subject }}</td>
                                <td>{{ $t->requester->name ?? $t->requester_name ?? '—' }}</td>
                                <td><span class="badge bg-{{ $t->priorityColor() }}">{{ ucfirst($t->priority) }}</span></td>
                                <td><span class="badge bg-{{ $t->statusColor() }}">{{ ucfirst(str_replace('_', ' ', $t->status)) }}</span></td>
                                <td>{{ $t->assignee->name ?? 'Unassigned' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.support-tickets.show', $t->id) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">{{ $tickets->links() }}</div>
        @endif
    </div>
</div>

@endsection
