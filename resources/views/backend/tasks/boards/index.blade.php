@extends('backend.layouts.master')

@section('title', 'Task Boards')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Task Management</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Task Boards</h4>
            <p class="subtitle">Kanban boards for tracking tasks across your team.</p>
        </div>
        @can('tasks.create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newBoardModal">
                <i class="bi bi-plus-lg me-1"></i> New Board
            </button>
        @endcan
    </div>
@endsection

@section('admin-content')

<div class="row g-3">
    @forelse ($boards as $board)
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('admin.tasks.boards.show', $board->id) }}" class="text-decoration-none">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="rounded-circle" style="width:12px;height:12px;background:{{ $board->color }};display:inline-block;"></span>
                            <h6 class="mb-0 text-dark">{{ $board->name }}</h6>
                        </div>
                        <p class="small text-muted mb-2">{{ $board->description ?: 'No description' }}</p>
                        <span class="badge bg-light text-dark border"><i class="bi bi-card-checklist me-1"></i>{{ $board->tasks_count }} tasks</span>
                    </div>
                </div>
            </a>
        </div>
    @empty
        <div class="col-12">
            <x-admin.empty-state
                icon="bi-kanban"
                title="No boards yet"
                description="Create a board to start organizing tasks for your team." />
        </div>
    @endforelse
</div>

<div class="modal fade" id="newBoardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.tasks.boards.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title">New Board</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label small fw-medium">Board Name</label>
                    <input type="text" name="name" class="form-control mb-3" placeholder="e.g. Website Redesign" required>

                    <label class="form-label small fw-medium">Description</label>
                    <textarea name="description" class="form-control mb-3" rows="2"></textarea>

                    <label class="form-label small fw-medium">Color</label>
                    <input type="color" name="color" value="#aa8038" class="form-control form-control-color">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Board</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
