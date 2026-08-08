@php
    $total = $checklist->items->count();
    $done = $checklist->items->where('is_completed', true)->count();
    $pct = $total ? round(($done / $total) * 100) : 0;
@endphp
<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-1">
        <span class="small fw-medium">{{ $checklist->title }}</span>
        <button type="button" class="btn btn-sm btn-link text-danger p-0 delete-checklist-btn" data-checklist-id="{{ $checklist->id }}">
            <i class="bi bi-trash small"></i>
        </button>
    </div>
    @if ($total > 0)
        <div class="progress mb-2" style="height:6px;">
            <div class="progress-bar" style="width:{{ $pct }}%;"></div>
        </div>
    @endif
    @foreach ($checklist->items as $item)
        <div class="d-flex align-items-center gap-2 mb-1">
            <input type="checkbox" class="form-check-input checklist-item-check" data-item-id="{{ $item->id }}" {{ $item->is_completed ? 'checked' : '' }}>
            <span class="small {{ $item->is_completed ? 'text-decoration-line-through text-muted' : '' }}">{{ $item->title }}</span>
            <button type="button" class="btn btn-sm btn-link text-muted p-0 ms-auto delete-checklist-item-btn" data-item-id="{{ $item->id }}">
                <i class="bi bi-x-lg small"></i>
            </button>
        </div>
    @endforeach
    <form class="add-checklist-item-form d-flex gap-1 mt-1" data-checklist-id="{{ $checklist->id }}">
        <input type="text" class="form-control form-control-sm" placeholder="+ Add an item">
    </form>
</div>
