@extends('backend.layouts.master')

@section('title', 'Media Details')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.media.index') }}">Media Library</a></li>
            <li class="breadcrumb-item active text-truncate" style="max-width:260px;">{{ $item->original_name }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="text-truncate" style="max-width:520px;">{{ $item->original_name }}</h4>
            <p class="subtitle">Uploaded {{ $item->created_at->format('d M Y, h:i A') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.media.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Library
            </a>
            @can('media.delete')
                <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('delete-media-{{ $item->id }}', '{{ $item->original_name }}')">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
                <form id="delete-media-{{ $item->id }}" action="{{ route('admin.media.destroy', $item->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            @endcan
        </div>
    </div>
@endsection

@section('admin-content')

<div class="row g-3">
    <!-- Preview -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center bg-light rounded p-3" style="min-height:420px;">
                    <img src="{{ $item->url }}" alt="{{ $item->alt_text ?? $item->original_name }}" class="img-fluid rounded" style="max-height:520px; object-fit:contain;">
                </div>
            </div>
        </div>
    </div>

    <!-- Details -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title mb-3">File Details</h6>
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted fw-normal">File name</dt>
                    <dd class="col-7 text-break">{{ $item->original_name }}</dd>

                    <dt class="col-5 text-muted fw-normal">Type</dt>
                    <dd class="col-7">
                        <span class="badge bg-light text-dark border">{{ $item->mime_type }}</span>
                    </dd>

                    <dt class="col-5 text-muted fw-normal">Size</dt>
                    <dd class="col-7">{{ $item->human_size }}</dd>

                    <dt class="col-5 text-muted fw-normal">Folder</dt>
                    <dd class="col-7">{{ $item->folder ?? '—' }}</dd>

                    <dt class="col-5 text-muted fw-normal">Uploaded by</dt>
                    <dd class="col-7">{{ $item->uploadedBy->name ?? '—' }}</dd>

                    <dt class="col-5 text-muted fw-normal">Uploaded on</dt>
                    <dd class="col-7">{{ $item->created_at->format('d M Y, h:i A') }}</dd>
                </dl>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <h6 class="card-title mb-3">File URL</h6>
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm" value="{{ $item->url }}" id="mediaUrl" readonly>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyUrl('{{ $item->url }}', this)">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>
                <div class="form-text">Paste this URL into any content field across the panel.</div>
            </div>
        </div>

        @can('media.upload')
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">Alt Text</h6>
                    <form id="altTextForm">
                        @csrf
                        <textarea name="alt_text" id="altText" class="form-control form-control-sm" rows="3" placeholder="Describe this image for accessibility & SEO...">{{ $item->alt_text }}</textarea>
                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="bi bi-check2 me-1"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan
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

    const altTextForm = document.getElementById('altTextForm');
    if (altTextForm) {
        altTextForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Saving...';

            fetch('{{ route('admin.media.update', $item->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'X-HTTP-Method-Override': 'PUT'
                },
                body: JSON.stringify({ alt_text: document.getElementById('altText').value })
            })
                .then(res => res.json())
                .then(() => {
                    btn.innerHTML = '<i class="bi bi-check2 me-1"></i> Saved';
                    setTimeout(() => { btn.innerHTML = originalHtml; btn.disabled = false; }, 1200);
                })
                .catch(() => {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                    alert('Could not save alt text. Please try again.');
                });
        });
    }
</script>
@endsection
