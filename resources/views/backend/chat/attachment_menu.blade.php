{{--
    Drop this include just above (or inside) the existing
    <div class="p-3"><form id="sendMessageForm">...</form></div>
    block in resources/views/backend/messages/index.blade.php, e.g.:

        <div class="border-top">
            ...reply preview bar...
            @include('backend.chat.attachment_menu')
            <div class="p-3">
                <form id="sendMessageForm" ...>
            ...
--}}

<div class="px-3 pt-2 position-relative">
    <div class="dropdown">
        <button type="button" class="btn btn-light btn-sm" id="attachMenuBtn" data-bs-toggle="dropdown" title="Attach">
            <i class="bi bi-plus-lg"></i>
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item small" href="#" id="pickDocument"><i class="bi bi-file-earmark-text me-2"></i>Document</a></li>
            <li><a class="dropdown-item small" href="#" id="pickMedia"><i class="bi bi-image me-2"></i>Photos &amp; videos</a></li>
            <li><a class="dropdown-item small" href="#" id="pickCamera"><i class="bi bi-camera me-2"></i>Camera</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item small" href="#" data-bs-toggle="modal" data-bs-target="#contactModal"><i class="bi bi-person me-2"></i>Contact</a></li>
            <li><a class="dropdown-item small" href="#" data-bs-toggle="modal" data-bs-target="#pollModal"><i class="bi bi-bar-chart me-2"></i>Poll</a></li>
            <li><a class="dropdown-item small" href="#" data-bs-toggle="modal" data-bs-target="#eventModal"><i class="bi bi-calendar-event me-2"></i>Event</a></li>
        </ul>
    </div>

    {{-- Mic / recording control, sits next to the + button --}}
    <button type="button" class="btn btn-light btn-sm ms-1" id="micBtn" title="Record voice message">
        <i class="bi bi-mic"></i>
    </button>

    <span id="recordingIndicator" class="d-none small text-danger ms-2">
        <i class="bi bi-record-circle-fill blinking-dot"></i> <span id="recordingTimer">0:00</span> — click mic again to send, or
        <a href="#" id="cancelRecordingBtn" class="text-muted">cancel</a>
    </span>

    {{-- Hidden native pickers --}}
    <input type="file" id="documentInput" class="d-none" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar,.csv">
    <input type="file" id="mediaInput" class="d-none" accept="image/*,video/*" multiple>
    <input type="file" id="cameraInput" class="d-none" accept="image/*" capture="environment">
</div>

<!-- Contact Modal -->
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="contactForm">
                <div class="modal-header"><h6 class="modal-title">Share a contact</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-2"><label class="form-label small">Name</label><input required name="name" class="form-control form-control-sm"></div>
                    <div class="mb-2"><label class="form-label small">Phone</label><input name="phone" class="form-control form-control-sm" placeholder="Optional if email is given"></div>
                    <div class="mb-2"><label class="form-label small">Email</label><input type="email" name="email" class="form-control form-control-sm" placeholder="Optional if phone is given"></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary btn-sm">Send</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Poll Modal -->
<div class="modal fade" id="pollModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="pollForm">
                <div class="modal-header"><h6 class="modal-title">Create a poll</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-2"><label class="form-label small">Question</label><input required name="question" class="form-control form-control-sm"></div>
                    <label class="form-label small">Options</label>
                    <div id="pollOptions">
                        <input name="options[]" class="form-control form-control-sm mb-2" placeholder="Option 1">
                        <input name="options[]" class="form-control form-control-sm mb-2" placeholder="Option 2">
                    </div>
                    <button type="button" class="btn btn-sm btn-link px-0" id="addPollOption">+ Add option</button>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="multiple" value="1" id="pollMultiple">
                        <label class="form-check-label small" for="pollMultiple">Allow multiple answers</label>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary btn-sm">Create poll</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Event Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="eventForm">
                <div class="modal-header"><h6 class="modal-title">Create an event</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="mb-2"><label class="form-label small">Title</label><input required name="title" class="form-control form-control-sm"></div>
                    <div class="mb-2"><label class="form-label small">Date &amp; time</label><input required type="datetime-local" name="starts_at" class="form-control form-control-sm"></div>
                    <div class="mb-2"><label class="form-label small">Location</label><input name="location" class="form-control form-control-sm"></div>
                    <div class="mb-2"><label class="form-label small">Description</label><textarea name="description" rows="2" class="form-control form-control-sm"></textarea></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary btn-sm">Send</button></div>
            </form>
        </div>
    </div>
</div>

<style>
    .blinking-dot { animation: chatBlink 1s infinite; }
    @keyframes chatBlink { 50% { opacity: 0.2; } }
</style>

<script>
(function () {
    const conversationId = document.getElementById('messageList')?.dataset.conversationId;
    if (!conversationId) return;

    const csrf = '{{ csrf_token() }}';
    const list = document.getElementById('messageList');

    function scrollToBottom() { if (list) list.scrollTop = list.scrollHeight; }

    // ---- Shared bubble renderer -------------------------------------
    // Mirrors backend.chat.message.blade.php closely enough for a live
    // client-side echo. It intentionally does NOT try to reproduce the
    // hover toolbar (reply/pin/star/etc.) — those actions operate on
    // persisted messages and your existing delegated-event handlers
    // already bind to .chat-message-row, which this markup provides.
    window.renderChatBubble = function (msg, isMine) {
        const existing = list.querySelector(`[data-message-id="${msg.id}"]`);
        let bodyHtml = '';

        if (msg.type === 'image') {
            bodyHtml = `<a href="${msg.attachment_url}" target="_blank" class="d-block mb-1">
                <img src="${msg.attachment_url}" class="rounded" style="max-width:260px;max-height:260px;object-fit:cover;"></a>`;
        } else if (msg.type === 'video') {
            bodyHtml = `<video controls class="rounded mb-1" style="max-width:280px;max-height:280px;">
                <source src="${msg.attachment_url}" type="${msg.attachment_mime || ''}"></video>`;
        } else if (msg.type === 'audio') {
            bodyHtml = `<div class="d-flex align-items-center gap-2 mb-1" style="min-width:220px;">
                <i class="bi bi-mic-fill"></i>
                <audio controls style="height:36px;"><source src="${msg.attachment_url}" type="${msg.attachment_mime || ''}"></audio></div>`;
        } else if (msg.type === 'document') {
            bodyHtml = `<a href="${msg.attachment_url}" target="_blank" download
                class="d-flex align-items-center gap-2 p-2 rounded ${isMine ? 'bg-primary border border-light border-opacity-25 text-white' : 'bg-white border text-dark'} text-decoration-none mb-1">
                <i class="bi bi-file-earmark-arrow-down fs-4"></i>
                <div class="overflow-hidden"><div class="text-truncate small fw-medium" style="max-width:200px;">${msg.attachment_name || 'File'}</div>
                <div class="small ${isMine ? 'text-white-50' : 'text-muted'}">${msg.attachment_size || ''}</div></div></a>`;
        } else if (msg.type === 'contact') {
            const c = msg.meta || {};
            bodyHtml = `<div class="d-flex align-items-center gap-2 p-2 rounded ${isMine ? 'bg-primary border border-light border-opacity-25' : 'bg-white border'} mb-1" style="min-width:220px;">
                <div class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;"><i class="bi bi-person-fill"></i></div>
                <div class="overflow-hidden"><div class="fw-medium text-truncate">${c.name || 'Contact'}</div>
                ${c.phone ? `<a class="d-block small text-decoration-none" href="tel:${c.phone}">${c.phone}</a>` : ''}
                ${c.email ? `<a class="d-block small text-decoration-none" href="mailto:${c.email}">${c.email}</a>` : ''}</div></div>`;
        } else if (msg.type === 'event') {
            const e = msg.meta || {};
            const when = e.starts_at ? new Date(e.starts_at).toLocaleString('en-IN', { dateStyle: 'medium', timeStyle: 'short', timeZone: 'Asia/Kolkata' }) : '';
            bodyHtml = `<div class="rounded p-2 ${isMine ? 'bg-primary border border-light border-opacity-25' : 'bg-white border'} mb-1" style="min-width:220px;">
                <div class="d-flex align-items-center gap-2 mb-1"><i class="bi bi-calendar-event"></i><span class="fw-medium">${e.title || 'Event'}</span></div>
                ${when ? `<div class="small">${when}</div>` : ''}
                ${e.location ? `<div class="small"><i class="bi bi-geo-alt"></i> ${e.location}</div>` : ''}
                ${e.description ? `<div class="small mt-1">${e.description}</div>` : ''}</div>`;
        } else if (msg.type === 'poll') {
            const p = msg.meta || {};
            const myVote = p.my_vote;
            bodyHtml = `<div class="rounded p-2 ${isMine ? 'bg-primary border border-light border-opacity-25' : 'bg-white border'} mb-1 poll-block" data-message-id="${msg.id}" style="min-width:240px;">
                <div class="d-flex align-items-center gap-2 mb-2"><i class="bi bi-bar-chart-fill"></i><span class="fw-medium">${p.question || ''}</span></div>
                ${(p.options || []).map(opt => {
                    const voted = Array.isArray(myVote) ? myVote.includes(opt.id) : myVote === opt.id;
                    return `<button type="button" class="poll-option-btn btn btn-sm w-100 text-start mb-1 position-relative overflow-hidden ${isMine ? 'btn-light' : 'btn-outline-secondary'} ${voted ? 'border-primary' : ''}" data-option-id="${opt.id}">
                        <span class="position-absolute top-0 start-0 h-100 bg-primary bg-opacity-25" style="width:${opt.percentage}%;z-index:0;"></span>
                        <span class="position-relative d-flex justify-content-between" style="z-index:1;"><span>${voted ? '✓ ' : ''}${opt.text}</span><span class="text-muted">${opt.percentage}%</span></span></button>`;
                }).join('')}
                <div class="small ${isMine ? 'text-white-50' : 'text-muted'} mt-1">${p.total_voters || 0} vote${(p.total_voters === 1) ? '' : 's'}${p.multiple ? ' · Select one or more' : ''}</div></div>`;
        }

        const bodyLine = msg.body ? `<div>${escapeHtml(msg.body)}</div>` : '';

        const html = `
            <div class="chat-bubble-wrap" style="max-width:70%;">
                <div class="${isMine ? 'bg-primary text-white' : 'bg-light'} rounded p-2 px-3">
                    ${bodyHtml}${bodyLine}
                    <div class="small ${isMine ? 'text-white-50' : 'text-muted'} mt-1">${msg.created_at || ''}</div>
                </div>
            </div>`;

        if (existing) {
            // Same message already in the DOM (e.g. a poll vote update) — refresh in place.
            existing.outerHTML = `<div class="d-flex mb-3 ${isMine ? 'justify-content-end' : ''} chat-message-row" data-message-id="${msg.id}" data-message-type="${msg.type}">${html}</div>`;
        } else {
            const wrap = document.createElement('div');
            wrap.className = `d-flex mt-5 mb-3 ${isMine ? 'justify-content-end' : ''} chat-message-row`;
            wrap.dataset.messageId = msg.id;
            wrap.dataset.messageType = msg.type;
            wrap.innerHTML = html;
            list.appendChild(wrap);
            scrollToBottom();
        }
    };

    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    // ---- Generic uploader (documents / photos / videos / camera) ----
    function uploadAttachment(file, forceType) {
        const fd = new FormData();
        fd.append('attachment', file);
        if (forceType) fd.append('force_type', forceType);

        fetch(`{{ url('admin/messages') }}/${conversationId}/send`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf },
            body: fd,
        })
            .then(res => res.json())
            .then(data => { if (data.success) window.renderChatBubble(data.message, true); });
    }

    document.getElementById('pickDocument')?.addEventListener('click', e => { e.preventDefault(); document.getElementById('documentInput').click(); });
    document.getElementById('pickMedia')?.addEventListener('click', e => { e.preventDefault(); document.getElementById('mediaInput').click(); });
    document.getElementById('pickCamera')?.addEventListener('click', e => { e.preventDefault(); document.getElementById('cameraInput').click(); });

    document.getElementById('documentInput')?.addEventListener('change', function () {
        if (this.files[0]) uploadAttachment(this.files[0], 'document');
        this.value = '';
    });
    document.getElementById('mediaInput')?.addEventListener('change', function () {
        Array.from(this.files).forEach(f => uploadAttachment(f)); // type auto-detected from mime
        this.value = '';
    });
    document.getElementById('cameraInput')?.addEventListener('change', function () {
        if (this.files[0]) uploadAttachment(this.files[0]);
        this.value = '';
    });

    // ---- Poll ---------------------------------------------------------
    document.getElementById('addPollOption')?.addEventListener('click', function () {
        const wrap = document.getElementById('pollOptions');
        const input = document.createElement('input');
        input.name = 'options[]';
        input.className = 'form-control form-control-sm mb-2';
        input.placeholder = `Option ${wrap.children.length + 1}`;
        wrap.appendChild(input);
    });

    document.getElementById('pollForm')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const fd = new FormData(this);
        fetch(`{{ url('admin/messages') }}/${conversationId}/poll`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf }, body: fd })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.renderChatBubble(data.message, true);
                    this.reset();
                    bootstrap.Modal.getInstance(document.getElementById('pollModal'))?.hide();
                }
            });
    });

    // ---- Contact --------------------------------------------------------
    document.getElementById('contactForm')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const fd = new FormData(this);
        fetch(`{{ url('admin/messages') }}/${conversationId}/contact`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf }, body: fd })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.renderChatBubble(data.message, true);
                    this.reset();
                    bootstrap.Modal.getInstance(document.getElementById('contactModal'))?.hide();
                }
            });
    });

    // ---- Event ----------------------------------------------------------
    document.getElementById('eventForm')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const fd = new FormData(this);
        fetch(`{{ url('admin/messages') }}/${conversationId}/event`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf }, body: fd })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.renderChatBubble(data.message, true);
                    this.reset();
                    bootstrap.Modal.getInstance(document.getElementById('eventModal'))?.hide();
                }
            });
    });

    // ---- Poll voting (delegated — bubbles are added dynamically) --------
    list.addEventListener('click', function (e) {
        const btn = e.target.closest('.poll-option-btn');
        if (!btn) return;
        const messageId = btn.closest('.poll-block')?.dataset.messageId;
        const optionId = btn.dataset.optionId;
        if (!messageId || !optionId) return;

        fetch(`{{ url('admin/messages/poll') }}/${messageId}/vote`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ option_id: optionId }),
        })
            .then(res => res.json())
            .then(data => { if (data.success) window.renderChatBubble(data.message, data.message.sender_id == {{ auth('admin')->id() }}); });
    });

    // ---- Mic recorder -----------------------------------------------
    let mediaRecorder = null, audioChunks = [], recordStart = null, timerInterval = null;

    function fmtTimer(sec) {
        const m = Math.floor(sec / 60), s = sec % 60;
        return `${m}:${s.toString().padStart(2, '0')}`;
    }

    document.getElementById('micBtn')?.addEventListener('click', async function () {
        const icon = this.querySelector('i');

        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder.stop(); // triggers onstop → upload
            return;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            audioChunks = [];
            mediaRecorder = new MediaRecorder(stream);

            mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
            mediaRecorder.onstop = function () {
                clearInterval(timerInterval);
                document.getElementById('recordingIndicator').classList.add('d-none');
                icon.className = 'bi bi-mic';
                stream.getTracks().forEach(t => t.stop());

                if (this._cancelled) { this._cancelled = false; return; }

                const duration = Math.round((Date.now() - recordStart) / 1000);
                const blob = new Blob(audioChunks, { type: 'audio/webm' });
                const file = new File([blob], `voice-note-${Date.now()}.webm`, { type: 'audio/webm' });

                const fd = new FormData();
                fd.append('attachment', file);
                fd.append('force_type', 'audio');
                fd.append('duration', duration);

                fetch(`{{ url('admin/messages') }}/${conversationId}/send`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf }, body: fd })
                    .then(res => res.json())
                    .then(data => { if (data.success) window.renderChatBubble(data.message, true); });
            };

            mediaRecorder.start();
            recordStart = Date.now();
            icon.className = 'bi bi-stop-fill text-danger';
            document.getElementById('recordingIndicator').classList.remove('d-none');
            timerInterval = setInterval(() => {
                document.getElementById('recordingTimer').textContent = fmtTimer(Math.round((Date.now() - recordStart) / 1000));
            }, 500);
        } catch (err) {
            alert('Microphone access was blocked or is unavailable. Please allow microphone permission to record a voice message.');
        }
    });

    document.getElementById('cancelRecordingBtn')?.addEventListener('click', function (e) {
        e.preventDefault();
        if (mediaRecorder && mediaRecorder.state === 'recording') {
            mediaRecorder._cancelled = true;
            mediaRecorder.stop();
        }
    });
})();
</script>
