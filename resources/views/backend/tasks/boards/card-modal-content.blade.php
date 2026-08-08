<div class="modal-header">
    <h6 class="modal-title">
        <input type="text" id="cardTitleInput" class="form-control form-control-lg border-0 fw-medium p-0" value="{{ $task->title }}" style="min-width:400px;">
    </h6>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body" data-task-id="{{ $task->id }}">
    <div class="row g-3">
        <!-- Main column -->
        <div class="col-md-8">

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label small fw-medium">Priority</label>
                    <select id="cardPriority" class="form-select form-select-sm">
                        @foreach (['low', 'medium', 'high', 'urgent'] as $p)
                            <option value="{{ $p }}" {{ $task->priority === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-medium">Due Date</label>
                    <input type="date" id="cardDueDate" class="form-control form-control-sm" value="{{ $task->due_date?->format('Y-m-d') }}">
                </div>
            </div>

            <label class="form-label small fw-medium">Description</label>
            <textarea id="cardDescription" class="form-control mb-3" rows="3" placeholder="Add a more detailed description...">{{ $task->description }}</textarea>

            <!-- Checklists (To-Do Lists) -->
            <div id="checklistsContainer">
                @foreach ($task->checklists as $checklist)
                    @include('backend.tasks.boards.checklist', ['checklist' => $checklist])
                @endforeach
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary mb-3" id="addChecklistBtn">
                <i class="bi bi-plus-lg me-1"></i> Add Checklist
            </button>

            <!-- Attachments -->
            <h6 class="small text-uppercase text-muted mt-3">Attachments</h6>
            <div id="attachmentsList" class="mb-2">
                @foreach ($task->attachments as $a)
                    @include('backend.tasks.boards.attachment', ['attachment' => $a])
                @endforeach
            </div>
            <form id="attachmentForm">
                <input type="file" name="file" id="attachmentInput" class="form-control form-control-sm">
            </form>

            <!-- Comments -->
            <h6 class="small text-uppercase text-muted mt-4">Comments</h6>
            <div id="commentsList" class="mb-2">
                @foreach ($task->comments as $c)
                    @include('backend.tasks.boards.comment', ['comment' => $c, 'isMine' => $c->admin_id === auth('admin')->id()])
                @endforeach
            </div>
            <form id="commentForm" class="d-flex gap-2">
                <input type="text" name="body" class="form-control form-control-sm" placeholder="Write a comment...">
                <button type="submit" class="btn btn-sm btn-primary">Post</button>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <label class="form-label small fw-medium">Assignees</label>
            <div id="assigneesList" class="mb-3">
                @forelse ($task->assignees as $assignee)
                    <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1 mb-1 me-1" data-admin-id="{{ $assignee->id }}">
                        {{ $assignee->name }}
                        <i class="bi bi-x-lg small remove-assignee-btn" style="cursor:pointer;" data-admin-id="{{ $assignee->id }}"></i>
                    </span>
                @empty
                    <div class="small text-muted">No one assigned yet.</div>
                @endforelse
            </div>
            <select id="addAssigneeSelect" class="form-select form-select-sm mb-3">
                <option value="">+ Assign someone...</option>
                @foreach ($boardMembers as $m)
                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                @endforeach
            </select>

            <label class="form-label small fw-medium">Labels</label>
            <div id="labelsList" class="mb-2">
                @foreach ($task->board->labels as $label)
                    @php $attached = $task->labels->contains('id', $label->id); @endphp
                    <span class="badge label-toggle mb-1 me-1" data-label-id="{{ $label->id }}"
                          style="background:{{ $label->color }}; cursor:pointer; opacity:{{ $attached ? '1' : '0.35' }};">
                        {{ $label->name }}
                    </span>
                @endforeach
            </div>
            <form id="newLabelForm" class="d-flex gap-1">
                <input type="color" name="color" value="#6c757d" class="form-control form-control-color form-control-sm" style="width:38px;">
                <input type="text" name="name" class="form-control form-control-sm" placeholder="New label">
                <button type="submit" class="btn btn-sm btn-outline-secondary">Add</button>
            </form>

            <hr>

            <button type="button" class="btn btn-sm btn-outline-secondary w-100 mb-2" id="toggleCompleteBtn">
                <i class="bi bi-check2-circle me-1"></i>{{ $task->completed_at ? 'Mark Incomplete' : 'Mark Complete' }}
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger w-100" id="deleteTaskBtn">
                <i class="bi bi-trash me-1"></i>Delete Task
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    const CSRF = '{{ csrf_token() }}';
    const taskId = {{ $task->id }};
    const base = `{{ url('admin/tasks') }}/${taskId}`;

    function markDirty() { window.__cardModalDirty = true; }

    function postJson(url, body, method = 'POST') {
        markDirty();
        return fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: body ? JSON.stringify(body) : null,
        }).then(res => res.json());
    }

    let saveTimer;
    function debouncedUpdate(field, value) {
        clearTimeout(saveTimer);
        saveTimer = setTimeout(() => postJson(`${base}`, { [field]: value }, 'PUT'), 500);
    }

    document.getElementById('cardTitleInput').addEventListener('input', e => debouncedUpdate('title', e.target.value));
    document.getElementById('cardDescription').addEventListener('input', e => debouncedUpdate('description', e.target.value));
    document.getElementById('cardPriority').addEventListener('change', e => postJson(base, { priority: e.target.value }, 'PUT'));
    document.getElementById('cardDueDate').addEventListener('change', e => postJson(base, { due_date: e.target.value }, 'PUT'));

    document.getElementById('toggleCompleteBtn').addEventListener('click', function () {
        postJson(`${base}/toggle-complete`).then(() => window.location.reload());
    });

    document.getElementById('deleteTaskBtn').addEventListener('click', function () {
        if (!confirm('Delete this task?')) return;
        fetch(base, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF } })
            .then(() => window.location.reload());
    });

    // ---- Assignees ----
    document.getElementById('addAssigneeSelect').addEventListener('change', function () {
        if (!this.value) return;
        postJson(`${base}/toggle-assignee`, { admin_id: this.value }).then(() => window.location.reload());
    });
    document.querySelectorAll('.remove-assignee-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            postJson(`${base}/toggle-assignee`, { admin_id: this.dataset.adminId }).then(() => window.location.reload());
        });
    });

    // ---- Labels ----
    document.querySelectorAll('.label-toggle').forEach(el => {
        el.addEventListener('click', function () {
            postJson(`${base}/toggle-label`, { label_id: this.dataset.labelId }).then(() => window.location.reload());
        });
    });
    document.getElementById('newLabelForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const name = this.name.value, color = this.color.value;
        if (!name.trim()) return;
        postJson(`{{ url('admin/tasks/boards') }}/{{ $task->board_id }}/labels`, { name, color })
            .then(() => window.location.reload());
    });

    // ---- Checklists ----
    document.getElementById('addChecklistBtn').addEventListener('click', function () {
        postJson(`{{ url('admin/tasks') }}/${taskId}/checklists`, { title: 'Checklist' })
            .then(() => window.location.reload());
    });

    document.addEventListener('submit', function (e) {
        if (e.target.classList.contains('add-checklist-item-form')) {
            e.preventDefault();
            const input = e.target.querySelector('input');
            if (!input.value.trim()) return;
            postJson(`{{ url('admin/tasks/checklists') }}/${e.target.dataset.checklistId}/items`, { title: input.value })
                .then(() => window.location.reload());
        }
        if (e.target.id === 'commentForm') {
            e.preventDefault();
            const input = e.target.querySelector('input');
            if (!input.value.trim()) return;
            postJson(`{{ url('admin/tasks') }}/${taskId}/comments`, { body: input.value })
                .then(() => window.location.reload());
        }
    });

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('checklist-item-check')) {
            postJson(`{{ url('admin/tasks/checklist-items') }}/${e.target.dataset.itemId}/toggle`, null, 'PUT');
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('delete-checklist-btn')) {
            if (!confirm('Delete this checklist?')) return;
            fetch(`{{ url('admin/tasks/checklists') }}/${e.target.dataset.checklistId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF } })
                .then(() => window.location.reload());
        }
        if (e.target.classList.contains('delete-checklist-item-btn')) {
            fetch(`{{ url('admin/tasks/checklist-items') }}/${e.target.dataset.itemId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF } })
                .then(() => window.location.reload());
        }
        if (e.target.classList.contains('delete-comment-btn')) {
            fetch(`{{ url('admin/tasks/comments') }}/${e.target.dataset.commentId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF } })
                .then(() => window.location.reload());
        }
        if (e.target.classList.contains('delete-attachment-btn')) {
            fetch(`{{ url('admin/tasks/attachments') }}/${e.target.dataset.attachmentId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF } })
                .then(() => window.location.reload());
        }
    });

    // ---- Attachments ----
    document.getElementById('attachmentInput').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const formData = new FormData();
        formData.append('file', file);
        markDirty();
        fetch(`{{ url('admin/tasks') }}/${taskId}/attachments`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF },
            body: formData,
        }).then(() => window.location.reload());
    });
})();
</script>
