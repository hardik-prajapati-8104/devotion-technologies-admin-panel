@extends('backend.layouts.master')

@section('title', $ticket->ticket_number)

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.support-tickets.index') }}">Support Tickets</a></li>
            <li class="breadcrumb-item active">{{ $ticket->ticket_number }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>{{ $ticket->subject }}</h4>
            <p class="subtitle">
                {{ $ticket->ticket_number }} &middot; Opened {{ $ticket->created_at->format('d M Y, h:i A') }}
                by {{ $ticket->creator->name ?? '—' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.support-tickets.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            @can('support-tickets.delete')
                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('delete-ticket-{{ $ticket->id }}', '{{ $ticket->ticket_number }}')">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
                <form id="delete-ticket-{{ $ticket->id }}" action="{{ route('admin.support-tickets.destroy', $ticket->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            @endcan
        </div>
    </div>
@endsection

@section('admin-content')

<div class="row g-3">
    <!-- Thread -->
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title mb-2">Description</h6>
                <p class="mb-0">{{ $ticket->description }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title mb-3">Conversation</h6>

                @forelse ($ticket->replies as $reply)
                    <div class="d-flex gap-2 mb-3 {{ $reply->is_internal_note ? 'bg-warning bg-opacity-10 rounded p-2' : '' }}">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;">
                            {{ strtoupper(substr($reply->admin->name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-medium">{{ $reply->admin->name ?? 'Unknown' }}</span>
                                @if ($reply->is_internal_note)
                                    <span class="badge bg-warning text-dark">Internal Note</span>
                                @endif
                                <span class="small text-muted">{{ $reply->created_at->format('d M Y, h:i A') }}</span>
                            </div>
                            <div class="mt-1">{{ $reply->message }}</div>
                            @if ($reply->attachment)
                                <a href="{{ Storage::disk('public')->url($reply->attachment) }}" target="_blank" class="small">
                                    <i class="bi bi-paperclip"></i> Attachment
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No replies yet.</p>
                @endforelse

                <hr>

                <form action="{{ route('admin.support-tickets.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <textarea name="message" rows="3" class="form-control mb-2" placeholder="Write a reply..." required></textarea>
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <input type="file" name="attachment" class="form-control form-control-sm" style="max-width:220px;">
                            <div class="form-check mb-0">
                                <input type="checkbox" name="is_internal_note" value="1" class="form-check-input" id="internalNote">
                                <label class="form-check-label small" for="internalNote">Internal note (not visible to requester)</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-send me-1"></i> Reply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Meta -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title mb-3">Ticket Details</h6>

                @can('support-tickets.edit')
                    <form action="{{ route('admin.support-tickets.status', $ticket->id) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PUT')
                        <label class="form-label small fw-medium">Status</label>
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach (['open', 'in_progress', 'resolved', 'closed'] as $s)
                                <option value="{{ $s }}" {{ $ticket->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                            @endforeach
                        </select>
                    </form>

                    <form action="{{ route('admin.support-tickets.assign', $ticket->id) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PUT')
                        <label class="form-label small fw-medium">Assigned To</label>
                        <select name="assigned_to" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Unassigned</option>
                            @foreach ($admins as $a)
                                <option value="{{ $a->id }}" {{ $ticket->assigned_to == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </form>
                @else
                    <dl class="row small mb-3">
                        <dt class="col-5 text-muted fw-normal">Status</dt>
                        <dd class="col-7"><span class="badge bg-{{ $ticket->statusColor() }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></dd>
                        <dt class="col-5 text-muted fw-normal">Assigned To</dt>
                        <dd class="col-7">{{ $ticket->assignee->name ?? 'Unassigned' }}</dd>
                    </dl>
                @endcan

                <dl class="row small mb-0">
                    <dt class="col-5 text-muted fw-normal">Priority</dt>
                    <dd class="col-7"><span class="badge bg-{{ $ticket->priorityColor() }}">{{ ucfirst($ticket->priority) }}</span></dd>

                    <dt class="col-5 text-muted fw-normal">Category</dt>
                    <dd class="col-7">{{ $ticket->category ?? '—' }}</dd>

                    <dt class="col-5 text-muted fw-normal">Requester</dt>
                    <dd class="col-7">{{ $ticket->requesterName() }}</dd>

                    @if ($ticket->requester_email)
                        <dt class="col-5 text-muted fw-normal">Email</dt>
                        <dd class="col-7 text-break">{{ $ticket->requester_email }}</dd>
                    @endif

                    <dt class="col-5 text-muted fw-normal">Resolved</dt>
                    <dd class="col-7">{{ $ticket->resolved_at?->format('d M Y, h:i A') ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

@endsection
