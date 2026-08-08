@extends('backend.layouts.master')

@section('title', $board->name)

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.tasks.boards.index') }}">Task Boards</a></li>
            <li class="breadcrumb-item active">{{ $board->name }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="rounded-circle" style="width:14px;height:14px;background:{{ $board->color }};display:inline-block;"></span>
            <h4 class="mb-0">{{ $board->name }}</h4>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#boardMembersModal">
                <i class="bi bi-people me-1"></i> Members ({{ $board->members->count() + 1 }})
            </button>
            @if ($board->isOwner(auth('admin')->user()))
                <form action="{{ route('admin.tasks.boards.destroy', $board->id) }}" method="POST" onsubmit="return confirm('Delete this board and everything on it?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Delete Board</button>
                </form>
            @endif
        </div>
    </div>
@endsection

@section('admin-content')

<div class="kanban-board d-flex gap-3 overflow-auto pb-3" id="kanbanBoard" data-board-id="{{ $board->id }}" style="align-items:flex-start;">
    @foreach ($board->columns as $column)
        <div class="kanban-column bg-light rounded p-2 flex-shrink-0" style="width:290px;" data-column-id="{{ $column->id }}">
            <div class="d-flex justify-content-between align-items-center px-1 mb-2">
                <span class="fw-medium small text-uppercase text-muted">{{ $column->name }} <span class="badge bg-white text-dark border ms-1">{{ $column->tasks->count() }}</span></span>
                <div class="dropdown">
                    <button class="btn btn-sm btn-link text-muted p-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button type="button" class="dropdown-item small delete-column-btn" data-id="{{ $column->id }}">Delete column</button></li>
                    </ul>
                </div>
            </div>

            <div class="kanban-cards d-flex flex-column gap-2" data-column-id="{{ $column->id }}" style="min-height:20px;">
                @foreach ($column->tasks as $task)
                    @php $progress = $task->checklistProgress(); @endphp
                    <div class="card kanban-card shadow-sm" data-task-id="{{ $task->id }}" style="cursor:grab;">
                        <div class="card-body p-2">
                            @if ($task->labels->isNotEmpty())
                                <div class="d-flex gap-1 flex-wrap mb-1">
                                    @foreach ($task->labels as $label)
                                        <span class="badge" style="background:{{ $label->color }};">{{ $label->name }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="small fw-medium {{ $task->completed_at ? 'text-decoration-line-through text-muted' : '' }}">{{ $task->title }}</div>

                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge bg-{{ $task->priorityColor() }}">{{ ucfirst($task->priority) }}</span>
                                    @if ($task->due_date)
                                        <span class="badge {{ $task->isOverdue() ? 'bg-danger' : 'bg-light text-dark border' }}">
                                            <i class="bi bi-calendar-event"></i> {{ $task->due_date->format('d M') }}
                                        </span>
                                    @endif
                                    @if ($progress['total'] > 0)
                                        <span class="badge bg-light text-dark border"><i class="bi bi-check2-square"></i> {{ $progress['done'] }}/{{ $progress['total'] }}</span>
                                    @endif
                                </div>
                                <div class="d-flex" style="margin-right:-6px;">
                                    @foreach ($task->assignees->take(3) as $assignee)
                                        <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center border border-white"
                                             style="width:22px;height:22px;font-size:10px;margin-right:-6px;" title="{{ $assignee->name }}">
                                            {{ strtoupper(substr($assignee->name, 0, 1)) }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <form class="add-card-form mt-2 px-1" data-column-id="{{ $column->id }}">
                <input type="text" name="title" class="form-control form-control-sm" placeholder="+ Add a card">
            </form>
        </div>
    @endforeach

    <div class="flex-shrink-0" style="width:270px;">
        <form id="addColumnForm" class="d-flex gap-2">
            <input type="text" name="name" class="form-control form-control-sm" placeholder="+ Add column">
        </form>
    </div>
</div>

<!-- Card detail modal (content injected via AJAX) -->
<div class="modal fade" id="cardModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" id="cardModalContent">
            <div class="modal-body text-center py-5"><div class="spinner-border text-primary"></div></div>
        </div>
    </div>
</div>

<!-- Board members modal -->
<div class="modal fade" id="boardMembersModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Board Members</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if ($board->isOwner(auth('admin')->user()))
                    <form action="{{ route('admin.tasks.boards.add-member', $board->id) }}" method="POST" class="d-flex gap-2 mb-3">
                        @csrf
                        <select name="admin_id" class="form-select form-select-sm" required>
                            <option value="">Add a member...</option>
                            @foreach ($boardAdmins as $a)
                                @if (! in_array($a->id, $memberIds))
                                    <option value="{{ $a->id }}">{{ $a->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary text-nowrap">Add</button>
                    </form>
                @endif

                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="small">{{ $board->creator->name ?? 'Owner' }}</span>
                    <span class="badge bg-light text-dark border">Owner</span>
                </div>
                @foreach ($board->members as $member)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="small">{{ $member->name }} @if ($member->pivot->is_owner) <span class="badge bg-light text-dark border">Owner</span> @endif</span>
                        @if ($board->isOwner(auth('admin')->user()))
                            <form action="{{ route('admin.tasks.boards.remove-member', [$board->id, $member->id]) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-x-lg"></i></button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
(function () {
    const CSRF = '{{ csrf_token() }}';
    const boardId = document.getElementById('kanbanBoard').dataset.boardId;

    function postJson(url, body, method = 'POST') {
        return fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: body ? JSON.stringify(body) : null,
        }).then(res => res.json());
    }

    // ---- Drag & drop cards between/within columns ----
    document.querySelectorAll('.kanban-cards').forEach(list => {
        new Sortable(list, {
            group: 'kanban-cards',
            animation: 150,
            onEnd: function (evt) {
                const taskId = evt.item.dataset.taskId;
                const newColumnId = evt.to.dataset.columnId;
                const newPosition = evt.newIndex;

                postJson(`{{ url('admin/tasks') }}/${taskId}/move`, {
                    column_id: newColumnId,
                    position: newPosition,
                }).then(data => {
                    if (data.success) {
                        // Keep the visible card count badge in sync without a full reload.
                        document.querySelectorAll('.kanban-column').forEach(col => {
                            const count = col.querySelectorAll('.kanban-card').length;
                            const badge = col.querySelector('.badge.bg-white');
                            if (badge) badge.textContent = count;
                        });
                    }
                });
            },
        });
    });

    // ---- Drag & drop to reorder columns ----
    new Sortable(document.getElementById('kanbanBoard'), {
        animation: 150,
        handle: '.kanban-column',
        filter: '.kanban-cards, input, .flex-shrink-0:last-child',
        preventOnFilter: false,
        onEnd: function () {
            const ids = Array.from(document.querySelectorAll('.kanban-column')).map(c => c.dataset.columnId);
            postJson(`{{ url('admin/tasks/boards') }}/${boardId}/columns/reorder`, { column_ids: ids });
        },
    });

    // ---- Add column ----
    document.getElementById('addColumnForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const input = this.querySelector('input');
        if (!input.value.trim()) return;

        postJson(`{{ url('admin/tasks/boards') }}/${boardId}/columns`, { name: input.value })
            .then(data => { if (data.success) window.location.reload(); });
    });

    // ---- Delete column ----
    document.querySelectorAll('.delete-column-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            if (!confirm('Delete this column?')) return;
            fetch(`{{ url('admin/tasks/columns') }}/${this.dataset.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF },
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) window.location.reload();
                    else alert(data.message || 'Could not delete column.');
                });
        });
    });

    // ---- Add card ----
    document.querySelectorAll('.add-card-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const input = this.querySelector('input');
            if (!input.value.trim()) return;

            postJson(`{{ url('admin/tasks/columns') }}/${this.dataset.columnId}/tasks`, { title: input.value })
                .then(data => { if (data.success) window.location.reload(); });
        });
    });

    // ---- Open card modal ----
    document.addEventListener('click', function (e) {
        const card = e.target.closest('.kanban-card');
        if (!card) return;

        const modalEl = document.getElementById('cardModal');
        const modal = new bootstrap.Modal(modalEl);
        document.getElementById('cardModalContent').innerHTML = '<div class="modal-body text-center py-5"><div class="spinner-border text-primary"></div></div>';
        modal.show();

        fetch(`{{ url('admin/tasks') }}/${card.dataset.taskId}/card`)
            .then(res => res.text())
            .then(html => { document.getElementById('cardModalContent').innerHTML = html; });
    });

    // Refresh the board behind the scenes when the modal closes, so any
    // edits (title, priority, due date, assignees...) show up on the card.
    document.getElementById('cardModal').addEventListener('hidden.bs.modal', function () {
        if (window.__cardModalDirty) window.location.reload();
    });
})();
</script>
@endsection
