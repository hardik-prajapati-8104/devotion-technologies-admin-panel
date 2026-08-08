<div class="d-flex align-items-center justify-content-between py-1 border-bottom">
    <a href="{{ $attachment->url }}" target="_blank" class="small text-decoration-none">
        <i class="bi bi-{{ $attachment->isImage() ? 'image' : 'paperclip' }} me-1"></i>
        {{ $attachment->original_name }}
        <span class="text-muted">({{ $attachment->human_size }})</span>
    </a>
    <button type="button" class="btn btn-sm btn-link text-danger p-0 delete-attachment-btn" data-attachment-id="{{ $attachment->id }}">
        <i class="bi bi-trash small"></i>
    </button>
</div>
