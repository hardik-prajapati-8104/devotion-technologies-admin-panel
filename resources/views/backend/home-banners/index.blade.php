@extends('backend.layouts.master')

@section('title', 'Home Banner Management')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Home Banners</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Home Banner Management</h4>
            <p class="subtitle">Drag to reorder — the order here is the order they'll appear on the homepage.</p>
        </div>
        @can('home-banners.create')
            <a href="{{ route('admin.home-banners.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Banner
            </a>
        @endcan
    </div>
@endsection

@section('admin-content')

<div class="card">
    <div class="card-body">
        @if ($banners->isEmpty())
            <x-admin.empty-state
                icon="bi-images"
                title="No banners yet"
                description="Use the Add Banner button above to create your first homepage banner." />
        @else
            <div id="bannerList" class="d-flex flex-column gap-2">
                @foreach ($banners as $banner)
                    <div class="border rounded p-2 d-flex align-items-center gap-3" data-id="{{ $banner->id }}">
                        <i class="bi bi-grip-vertical text-muted" style="cursor:grab;"></i>

                        <img src="{{ $banner->image_url }}" class="rounded" style="width:110px; height:60px; object-fit:cover;">

                        <div class="flex-grow-1">
                            <div class="fw-medium">{{ $banner->title ?: '(No title)' }}</div>
                            <div class="small text-muted text-truncate" style="max-width:400px;">{{ $banner->subtitle }}</div>
                            @if ($banner->isScheduled())
                                <div class="small text-muted">
                                    <i class="bi bi-calendar-range"></i>
                                    {{ $banner->starts_at?->format('d M Y') ?? 'Anytime' }} &rarr; {{ $banner->ends_at?->format('d M Y') ?? 'No end' }}
                                </div>
                            @endif
                        </div>

                        <div class="text-center" style="width:90px;">
                            @can('home-banners.edit')
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input status-toggle" type="checkbox" data-id="{{ $banner->id }}" {{ $banner->status ? 'checked' : '' }}>
                                </div>
                            @else
                                <span class="badge {{ $banner->status ? 'bg-success' : 'bg-secondary' }}">{{ $banner->status ? 'Active' : 'Inactive' }}</span>
                            @endcan
                            <div class="small {{ $banner->isCurrentlyLive() ? 'text-success' : 'text-muted' }}">
                                {{ $banner->isCurrentlyLive() ? 'Live now' : 'Not showing' }}
                            </div>
                        </div>

                        <div class="d-flex gap-1">
                            @can('home-banners.edit')
                                <a href="{{ route('admin.home-banners.edit', $banner->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            @endcan
                            @can('home-banners.delete')
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('delete-banner-{{ $banner->id }}', '{{ $banner->title ?: 'this banner' }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <form id="delete-banner-{{ $banner->id }}" action="{{ route('admin.home-banners.destroy', $banner->id) }}" method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
    const list = document.getElementById('bannerList');
    if (list) {
        new Sortable(list, {
            handle: '.bi-grip-vertical',
            animation: 150,
            onEnd: function () {
                const ids = Array.from(list.children).map(el => el.dataset.id);
                fetch('{{ route('admin.home-banners.reorder') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ banner_ids: ids }),
                });
            },
        });
    }

    document.querySelectorAll('.status-toggle').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            const id = this.dataset.id;
            const checkbox = this;
            const previous = !checkbox.checked;

            fetch(`{{ url('admin/home-banners') }}/${id}/toggle-status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-HTTP-Method-Override': 'PUT' },
            })
                .then(res => res.json())
                .then(data => { if (!data.success) checkbox.checked = previous; })
                .catch(() => { checkbox.checked = previous; });
        });
    });
</script>
@endsection
