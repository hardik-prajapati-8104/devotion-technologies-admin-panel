@props(['icon' => 'bi-inbox', 'title' => 'Nothing here yet', 'description' => null, 'actionLabel' => null, 'actionUrl' => null])

<div class="text-center py-5">
    <i class="bi {{ $icon }}" style="font-size:2.5rem; color: var(--muted-color);"></i>
    <h6 class="fw-semibold mt-3 mb-1">{{ $title }}</h6>
    @if ($description)
        <p class="text-muted small mb-3">{{ $description }}</p>
    @endif
    @if ($actionLabel && $actionUrl)
        <a href="{{ $actionUrl }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>{{ $actionLabel }}
        </a>
    @endif
</div>
