@extends('backend.layouts.master')

@section('title', 'Recycle Bin')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Recycle Bin</li>
        </ol>
    </nav>
    <div>
        <h4>Recycle Bin</h4>
        <p class="subtitle">Deleted items across the panel — restore them, archive for safekeeping, or delete permanently.</p>
    </div>
@endsection

@section('admin-content')

<!-- Tabs -->
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ $status === 'trashed' ? 'active' : '' }}" href="{{ route('admin.recycle-bin.index', ['status' => 'trash']) }}">
            <i class="bi bi-trash3 me-1"></i> Trash
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $status === 'archived' ? 'active' : '' }}" href="{{ route('admin.recycle-bin.index', ['status' => 'archived']) }}">
            <i class="bi bi-archive me-1"></i> Archived
        </a>
    </li>
    @can('recycle-bin.manage')
        <li class="nav-item ms-auto">
            <button type="button" class="nav-link" data-bs-toggle="modal" data-bs-target="#retentionModal">
                <i class="bi bi-gear me-1"></i> Retention Settings
            </button>
        </li>
    @endcan
</ul>

<!-- Filter by type -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex align-items-center gap-2">
            <input type="hidden" name="status" value="{{ $status === 'archived' ? 'archived' : 'trash' }}">
            <label class="small text-muted mb-0">Type:</label>
            <select name="type" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">All types</option>
                @foreach ($registry as $class => $config)
                    <option value="{{ $class }}" {{ request('type') === $class ? 'selected' : '' }}>{{ $config['label'] }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<form id="bulkForm">
    <div class="card">
        <div class="card-body">
            @if ($metaRows->isEmpty())
                <x-admin.empty-state
                    :icon="$status === 'archived' ? 'bi-archive' : 'bi-trash3'"
                    :title="$status === 'archived' ? 'Nothing archived' : 'Trash is empty'"
                    description="" />
            @else
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                        <label class="form-check-label small" for="selectAll">Select all</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary bulk-action-btn" data-action="restore">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Restore
                        </button>
                        @if ($status === 'trashed')
                            <button type="button" class="btn btn-sm btn-outline-secondary bulk-action-btn" data-action="archive">
                                <i class="bi bi-archive me-1"></i>Archive
                            </button>
                        @else
                            <button type="button" class="btn btn-sm btn-outline-secondary bulk-action-btn" data-action="unarchive">
                                <i class="bi bi-arrow-return-left me-1"></i>Move to Trash
                            </button>
                        @endif
                        @can('recycle-bin.delete')
                            <button type="button" class="btn btn-sm btn-outline-danger bulk-action-btn" data-action="delete" data-confirm="Permanently delete the selected items? This cannot be undone.">
                                <i class="bi bi-x-lg me-1"></i>Delete Forever
                            </button>
                        @endcan
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th style="width:30px;"></th>
                                <th>Item</th>
                                <th>Type</th>
                                <th>Deleted By</th>
                                <th>Deleted At</th>
                                @if ($status === 'trashed')
                                    <th>Auto-delete</th>
                                @else
                                    <th>Archived By</th>
                                @endif
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($metaRows as $row)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input row-check" value="{{ $row->type }}::{{ $row->model->id }}">
                                    </td>
                                    <td class="fw-medium">{{ $row->title }}</td>
                                    <td><span class="badge bg-light text-dark border"><i class="bi {{ $row->icon }} me-1"></i>{{ $row->label }}</span></td>
                                    <td class="small">{{ $row->meta->deletedBy->name ?? '—' }}</td>
                                    <td class="small">{{ $row->meta->deleted_at?->format('d M Y, h:i A') }}</td>
                                    @if ($status === 'trashed')
                                        <td class="small">
                                            @if ($row->meta->auto_delete_at)
                                                @php $days = $row->meta->daysUntilAutoDelete(); @endphp
                                                <span class="badge {{ $days <= 3 ? 'bg-danger' : 'bg-light text-dark border' }}">
                                                    {{ $days }} day{{ $days === 1 ? '' : 's' }} left
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    @else
                                        <td class="small">{{ $row->meta->archivedBy->name ?? '—' }}</td>
                                    @endif
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end"> 

                                            @if ($status === 'trashed')
                                                <form action="{{ route('admin.recycle-bin.archive') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="type" value="{{ $row->type }}">
                                                    <input type="hidden" name="id" value="{{ $row->model->id }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Archive"><i class="bi bi-archive"></i></button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.recycle-bin.unarchive') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="type" value="{{ $row->type }}">
                                                    <input type="hidden" name="id" value="{{ $row->model->id }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Move to Trash"><i class="bi bi-arrow-return-left"></i></button>
                                                </form>
                                            @endif

                                            @can('recycle-bin.delete')
                                                <form action="{{ route('admin.recycle-bin.force-delete') }}" method="POST" onsubmit="return confirm('Permanently delete this item? This cannot be undone.')">
                                                    @csrf
                                                    <input type="hidden" name="type" value="{{ $row->type }}">
                                                    <input type="hidden" name="id" value="{{ $row->model->id }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Forever"><i class="bi bi-x-lg"></i></button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">{{ $metaRows->links() }}</div>
            @endif
        </div>
    </div>
</form>

@can('recycle-bin.manage')
    <div class="modal fade" id="retentionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.recycle-bin.settings') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title">Retention Settings</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label small fw-medium">Default retention period (days)</label>
                        <input type="number" name="default_retention_days" value="{{ $settings->default_retention_days }}" min="1" max="3650" class="form-control" required>
                        <div class="form-text">Trashed items are permanently deleted automatically after this many days. Archived items are never auto-deleted.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endcan

@endsection

@section('scripts')
<script>
    document.getElementById('selectAll')?.addEventListener('change', function () {
        document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked);
    });

    document.querySelectorAll('.bulk-action-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const selected = Array.from(document.querySelectorAll('.row-check:checked')).map(c => c.value);
            if (!selected.length) { alert('Select at least one item.'); return; }

            const confirmMsg = this.dataset.confirm;
            if (confirmMsg && !confirm(confirmMsg)) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('admin.recycle-bin.bulk') }}';

            const csrf = document.createElement('input');
            csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const action = document.createElement('input');
            action.type = 'hidden'; action.name = 'action'; action.value = this.dataset.action;
            form.appendChild(action);

            selected.forEach((val, i) => {
                const [type, id] = val.split('::');
                const typeInput = document.createElement('input');
                typeInput.type = 'hidden'; typeInput.name = `items[${i}][type]`; typeInput.value = type;
                form.appendChild(typeInput);

                const idInput = document.createElement('input');
                idInput.type = 'hidden'; idInput.name = `items[${i}][id]`; idInput.value = id;
                form.appendChild(idInput);
            });

            document.body.appendChild(form);
            form.submit();
        });
    });
</script>
@endsection
