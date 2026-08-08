<div class="d-flex gap-2 mb-2">
    <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center flex-shrink-0" style="width:28px;height:28px;font-size:11px;">
        {{ strtoupper(substr($comment->admin->name ?? '?', 0, 1)) }}
    </div>
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2">
            <span class="small fw-medium">{{ $comment->admin->name ?? 'Unknown' }}</span>
            <span class="small text-muted">{{ $comment->created_at->format('d M, h:i A') }}</span>
            @if ($isMine)
                <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-auto delete-comment-btn" data-comment-id="{{ $comment->id }}">
                    <i class="bi bi-trash small"></i>
                </button>
            @endif
        </div>
        <div class="small">{{ $comment->body }}</div>
    </div>
</div>
