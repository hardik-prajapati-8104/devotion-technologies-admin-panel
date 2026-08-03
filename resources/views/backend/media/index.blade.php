@extends('backend.layouts.master')

@section('title', 'Media Library')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Media Library</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Media Library</h4>
            <p class="subtitle">Upload once, reuse everywhere — copy a URL into any content field across the panel.</p>
        </div>
        @can('media.upload')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="bi bi-cloud-upload me-1"></i> Upload Images
            </button>
        @endcan
    </div>
@endsection

@section('admin-content')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label small fw-medium">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by filename...">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-medium">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Images</option>
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
        @if ($media->isEmpty())
            <x-admin.empty-state
                icon="bi-images"
                title="No media uploaded yet"
                description="Use the Upload Images button above to build a reusable library for the rest of the panel." />
        @else
            <div class="row g-3">
                @foreach ($media as $item)
                    <div class="col-6 col-md-3 col-xl-2">
                        <div class="border rounded p-2 h-100 d-flex flex-column">
                            <img src="{{ $item->url }}" class="rounded mb-2" style="width:100%; height:100px; object-fit:cover;">
                            <div class="small text-truncate" title="{{ $item->original_name }}">{{ $item->original_name }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $item->human_size }}</div>
                            <div class="d-flex gap-1 mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="copyUrl('{{ $item->url }}', this)">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                                @can('media.delete')
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('delete-media-{{ $item->id }}', '{{ $item->original_name }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="delete-media-{{ $item->id }}" action="{{ route('admin.media.destroy', $item->id) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endcan
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">{{ $media->links() }}</div>
        @endif
    </div>
</div>

<!-- Upload modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title">Upload Images</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label small fw-medium">Choose one or more images</label>
                    <input type="file" name="files[]" class="form-control" accept="image/png,image/jpeg,image/webp" multiple required>
                    <div class="form-text">JPG, PNG, or WEBP. Max 4MB each.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyUrl(url, button) {
        navigator.clipboard.writeText(url).then(() => {
            const icon = button.querySelector('i');
            icon.className = 'bi bi-check2';
            setTimeout(() => { icon.className = 'bi bi-clipboard'; }, 1500);
        });
    }
</script>
@endsection
