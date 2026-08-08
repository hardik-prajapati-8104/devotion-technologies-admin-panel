@extends('backend.layouts.master')

@section('title', 'Backup Management')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Backups</li>
        </ol>
    </nav>
    <div>
        <h4>Backup Management</h4>
        <p class="subtitle">Database, file, and scheduled backups.</p>
    </div>
@endsection

@section('admin-content')

@if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="d-flex gap-2 mb-3 flex-wrap">
    <form method="POST" action="{{ route('admin.backups.database') }}">
        @csrf
        <button class="btn btn-outline-primary btn-sm"><i class="bi bi-database me-1"></i>Backup Database</button>
    </form>
    <form method="POST" action="{{ route('admin.backups.files') }}">
        @csrf
        <button class="btn btn-outline-primary btn-sm"><i class="bi bi-folder me-1"></i>Backup Files</button>
    </form>
    <form method="POST" action="{{ route('admin.backups.full') }}">
        @csrf
        <button class="btn btn-primary btn-sm"><i class="bi bi-archive me-1"></i>Full Backup (DB + Files)</button>
    </form>
    <form method="POST" action="{{ route('admin.backups.prune') }}" class="ms-auto">
        @csrf
        <button class="btn btn-outline-secondary btn-sm">Run Cleanup Now</button>
    </form>
</div>

<div class="card mb-4">
    <div class="card-header fw-medium">Scheduled Backup</div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.backups.settings') }}" class="row g-3">
            @csrf
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="schedule_enabled" value="1" id="se" {{ $settings->schedule_enabled ? 'checked' : '' }}>
                    <label class="form-check-label" for="se">Enable scheduled backups</label>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Frequency</label>
                <select name="frequency" class="form-select form-select-sm">
                    @foreach (['daily' => 'Daily', 'weekly' => 'Weekly (Monday)', 'monthly' => 'Monthly (1st)'] as $val => $label)
                        <option value="{{ $val }}" {{ $settings->frequency === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Time (server time, 24h)</label>
                <input type="time" name="time" class="form-control form-control-sm" value="{{ $settings->time }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Retention (days)</label>
                <input type="number" name="retention_days" class="form-control form-control-sm" min="0" max="3650" value="{{ $settings->retention_days }}">
                <div class="form-text">0 = keep forever.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Notify email (optional)</label>
                <input type="email" name="notify_email" class="form-control form-control-sm" value="{{ $settings->notify_email }}">
            </div>
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="backup_database" value="1" id="bd" {{ $settings->backup_database ? 'checked' : '' }}>
                    <label class="form-check-label small" for="bd">Include database</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="backup_files" value="1" id="bf" {{ $settings->backup_files ? 'checked' : '' }}>
                    <label class="form-check-label small" for="bf">Include files</label>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label small">File paths to include (one per line, relative to project root)</label>
                <textarea name="file_paths" rows="3" class="form-control form-control-sm">{{ $settings->file_paths }}</textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary btn-sm">Save Settings</button>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-sm align-middle">
        <thead><tr><th>File</th><th>Type</th><th>Size</th><th>Status</th><th>Source</th><th>Triggered by</th><th>Date</th><th></th></tr></thead>
        <tbody>
            @forelse ($backups as $b)
                <tr>
                    <td class="small">{{ $b->filename }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($b->type) }}</span></td>
                    <td class="small">{{ $b->humanSize() ?? '—' }}</td>
                    <td>
                        @if ($b->status === 'completed')<span class="badge bg-success">Completed</span>
                        @elseif ($b->status === 'failed')<span class="badge bg-danger" title="{{ $b->error_message }}">Failed</span>
                        @else<span class="badge bg-warning text-dark">Running</span>@endif
                    </td>
                    <td class="small text-muted">{{ ucfirst($b->source) }}</td>
                    <td class="small text-muted">{{ $b->triggeredBy?->name ?? 'System' }}</td>
                    <td class="small">{{ $b->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}</td>
                    <td class="text-end">
                        @if ($b->status === 'completed')
                            <a href="{{ route('admin.backups.download', $b) }}" class="btn btn-sm btn-outline-secondary" title="Download"><i class="bi bi-download"></i></a>
                            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#restoreModal{{ $b->id }}" title="Restore"><i class="bi bi-arrow-counterclockwise"></i></button>
                        @endif
                        <form method="POST" action="{{ route('admin.backups.destroy', $b) }}" class="d-inline" onsubmit="return confirm('Delete this backup permanently?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>

                <div class="modal fade" id="restoreModal{{ $b->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('admin.backups.restore', $b) }}">
                                @csrf
                                <div class="modal-header"><h6 class="modal-title text-danger">Restore {{ $b->filename }}?</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <div class="modal-body">
                                    <p>
                                        @if ($b->type === 'database')
                                            This will overwrite your current database with the contents of this backup.
                                        @elseif ($b->type === 'files')
                                            This will overwrite current files with the contents of this backup.
                                        @else
                                            This will overwrite both your database AND current files with the contents of this backup.
                                        @endif
                                        This cannot be undone — consider taking a fresh backup first.
                                    </p>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="confirm" value="1" required>
                                        <label class="form-check-label small">I understand and want to proceed.</label>
                                    </div>
                                </div>
                                <div class="modal-footer"><button class="btn btn-danger btn-sm">Restore</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <tr><td colspan="8" class="text-center text-muted small py-4">No backups yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $backups->links() }}

@endsection
