<style>
    .chat-message-row:hover .chat-actions-toolbar { display: flex !important; }
    .chat-actions-toolbar { font-size: .85rem; }
</style>

<div class="modal fade" id="forwardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Forward Message</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted">Select one or more conversations to forward this message to.</p>
                <div id="forwardTargetList" class="list-group" style="max-height:260px; overflow:auto;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmForwardBtn">Forward</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="messageInfoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Message Info</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="messageInfoBody" style="font-size: 18px;">
                <div class="text-muted">Loading...</div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const CSRF = '{{ csrf_token() }}';
    let replyingTo = null;   // { id, sender, body }
    let forwardMessageId = null;

    function post(url, body) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: body ? JSON.stringify(body) : null,
        }).then(res => res.json());
    }

    // ---- Reply ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.reply-btn');
        if (!btn) return;
        const row = btn.closest('.chat-message-row');
        const id = row.dataset.messageId;
        const sender = row.querySelector('.fw-medium')?.textContent?.trim() || 'Message';
        const bodyEl = row.querySelector('.bg-light, .bg-primary');
        const bodyText = bodyEl ? bodyEl.textContent.trim().slice(0, 80) : '';

        replyingTo = { id, sender, body: bodyText };
        const bar = document.getElementById('replyPreviewBar');
        if (bar) {
            document.getElementById('replyPreviewSender').textContent = sender;
            document.getElementById('replyPreviewBody').textContent = bodyText;
            bar.classList.remove('d-none');
        }
        document.getElementById('messageInput')?.focus();
    });

    document.getElementById('cancelReplyBtn')?.addEventListener('click', function () {
        replyingTo = null;
        document.getElementById('replyPreviewBar')?.classList.add('d-none');
    });

    window.getReplyToId = () => replyingTo?.id ?? null;
    window.clearReplyState = () => {
        replyingTo = null;
        document.getElementById('replyPreviewBar')?.classList.add('d-none');
    };

    // ---- Copy ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.copy-btn');
        if (!btn) return;
        const row = btn.closest('.chat-message-row');
        const bodyEl = row.querySelector('.bg-light, .bg-primary');
        const text = bodyEl ? Array.from(bodyEl.childNodes)
            .filter(n => n.nodeType === 3 || (n.nodeType === 1 && !n.classList.contains('small') && n.tagName !== 'A'))
            .map(n => n.textContent).join('').trim() : '';
        navigator.clipboard.writeText(text).then(() => {
            btn.innerHTML = '<i class="bi bi-check2"></i>';
            setTimeout(() => btn.innerHTML = '<i class="bi bi-clipboard"></i>', 1200);
        });
    });

    // ---- React ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.emoji-pick');
        if (!btn) return;
        const row = btn.closest('.chat-message-row');
        const id = row.dataset.messageId;
        post(`{{ url('admin/chat-messages') }}/${id}/react`, { emoji: btn.dataset.emoji })
            .then(() => location.reload()); // simplest reliable refresh of reaction pills
    });

    // ---- Pin ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.pin-btn');
        if (!btn) return;
        e.preventDefault();
        const row = btn.closest('.chat-message-row');
        const id = row.dataset.messageId;
        post(`{{ url('admin/chat-messages') }}/${id}/pin`).then(() => location.reload());
    });

    // ---- Star ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.star-btn');
        if (!btn) return;
        e.preventDefault();
        const row = btn.closest('.chat-message-row');
        const id = row.dataset.messageId;
        post(`{{ url('admin/chat-messages') }}/${id}/star`).then(() => location.reload());
    });

    // ---- Delete ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.delete-btn');
        if (!btn) return;
        e.preventDefault();
        if (!confirm('Delete this message for everyone?')) return;
        const row = btn.closest('.chat-message-row');
        const id = row.dataset.messageId;
        fetch(`{{ url('admin/chat-messages') }}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        }).then(() => row.remove());
    });

    // ---- Message Info ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.info-btn');
        if (!btn) return;
        e.preventDefault();
        const row = btn.closest('.chat-message-row');
        const id = row.dataset.messageId;
        const modal = new bootstrap.Modal(document.getElementById('messageInfoModal'));
        document.getElementById('messageInfoBody').innerHTML = '<div class="text-muted small">Loading...</div>';
        modal.show();

        fetch(`{{ url('admin/chat-messages') }}/${id}/info`).then(r => r.json()).then(info => {
            let html = `
                <p class="mb-2">${info.body ?? ''}</p>
                <dl class="row small mb-0">
                    <dt class="col-5 text-muted fw-normal">Sent by</dt><dd class="col-7">${info.sender}</dd>
                    <dt class="col-5 text-muted fw-normal">Sent at</dt><dd class="col-7">${info.sent_at}</dd>
                    ${info.edited_at ? `<dt class="col-5 text-muted fw-normal">Edited</dt><dd class="col-7">${info.edited_at}</dd>` : ''}
                    <dt class="col-5 text-muted fw-normal">Read</dt><dd class="col-7">${info.is_read ? 'Yes' : 'Not yet'}</dd>
                </dl>`;
            document.getElementById('messageInfoBody').innerHTML = html;
        });
    });

    // ---- Forward ----
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.forward-btn');
        if (!btn) return;
        e.preventDefault();
        const row = btn.closest('.chat-message-row');
        forwardMessageId = row.dataset.messageId;

        const list = document.getElementById('forwardTargetList');
        list.innerHTML = '';
        (window.__chatForwardTargets || []).forEach(t => {
            const item = document.createElement('label');
            item.className = 'list-group-item d-flex align-items-center gap-2';
            item.innerHTML = `<input type="checkbox" class="form-check-input me-2" value="${t.id}"> ${t.name}`;
            list.appendChild(item);
        });

        new bootstrap.Modal(document.getElementById('forwardModal')).show();
    });

    document.getElementById('confirmForwardBtn')?.addEventListener('click', function () {
        const ids = Array.from(document.querySelectorAll('#forwardTargetList input:checked')).map(i => parseInt(i.value));
        if (!ids.length || !forwardMessageId) return;

        post(`{{ url('admin/chat-messages') }}/${forwardMessageId}/forward`, { conversation_ids: ids })
            .then(() => {
                bootstrap.Modal.getInstance(document.getElementById('forwardModal')).hide();
                alert('Message forwarded.');
            });
    });
})();
</script>
