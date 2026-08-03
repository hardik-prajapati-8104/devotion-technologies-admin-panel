@extends('backend.layouts.master')

@section('title', 'FAQs')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">FAQs</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>FAQ Management</h4>
            <p class="subtitle">Drag rows to reorder how FAQs appear on the website.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.faq-categories.index') }}" class="btn btn-outline-secondary"><i class="bi bi-tags me-1"></i> Categories</a>
            @can('faqs.create')
                <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add FAQ</a>
            @endcan
        </div>
    </div>
@endsection

@section('admin-content')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-medium">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by question...">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-medium">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if ($faqs->isEmpty())
            <x-admin.empty-state
                icon="bi-patch-question"
                title="No FAQs found"
                description="Add your first frequently asked question."
                actionLabel="Add FAQ"
                :actionUrl="route('admin.faqs.create')" />
        @else
            <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle" id="sortable-table">
                <thead>
                    <tr>
                        <th width="2%"></th>
                        <th>Question</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th width="8%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($faqs as $faq)
                    <tr data-id="{{ $faq->id }}">
                        <td class="text-center text-muted" style="cursor:grab;"><i class="bi bi-grip-vertical"></i></td>
                        <td>{{ $faq->question }}</td>
                        <td>{{ $faq->category->name ?? '—' }}</td>
                        <td><x-admin.status-badge :status="$faq->status" active-label="Enabled" inactive-label="Disabled" /></td>
                        <td>
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">&#x22EE;</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @can('faqs.edit')
                                    <li><a class="dropdown-item" href="{{ route('admin.faqs.edit', $faq->id) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                @endcan
                                @can('faqs.delete')
                                    <li>
                                        <x-admin.confirm-delete
                                            :action="route('admin.faqs.destroy', $faq->id)"
                                            :form-id="'delete-faq-'.$faq->id"
                                            :label="$faq->question" />
                                    </li>
                                @endcan
                            </ul>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="mt-3">{{ $faqs->links() }}</div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    const sortableBody = document.querySelector('#sortable-table tbody');
    if (sortableBody) {
        Sortable.create(sortableBody, {
            handle: '.bi-grip-vertical',
            animation: 150,
            onEnd: function () {
                const order = Array.from(sortableBody.querySelectorAll('tr')).map(row => row.dataset.id);
                fetch('{{ route('admin.faqs.reorder') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ order }),
                });
            }
        });
    }
</script>
@endsection
