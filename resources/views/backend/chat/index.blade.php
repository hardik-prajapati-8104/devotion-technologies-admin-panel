@extends('backend.layouts.master')

@section('title', 'Internal Chat')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Internal Chat</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Internal Chat</h4>
            <p class="subtitle">Group channels for team and project discussions.</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newGroupModal">
            <i class="bi bi-plus-lg me-1"></i> New Group
        </button>
    </div>
@endsection

@section('admin-content')

<div class="card" style="height:75vh;">
    <div class="row g-0 h-100">
        <div class="col-md-4 col-lg-3 border-end h-100 d-flex flex-column">
            <div class="p-3 border-bottom">
                <input type="text" id="channelSearch" class="form-control form-control-sm" placeholder="Search channels...">
            </div>
            <div class="overflow-auto flex-grow-1">
                @forelse ($conversations as $c)
                    <a href="{{ route('admin.chat.index', ['conversation' => $c->id]) }}"
                       class="d-flex align-items-center gap-2 p-3 text-decoration-none border-bottom channel-item {{ $active && $active->id === $c->id ? 'bg-light' : '' }}">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width:38px;height:38px;">
                            <i class="bi bi-hash"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-medium text-dark text-truncate">{{ $c->name }}</div>
                            <div class="small text-muted text-truncate">{{ $c->latestMessage->body ?? 'No messages yet' }}</div>
                        </div>
                        @php $unread = $c->unreadCountFor(auth('admin')->user()); @endphp
                        @if ($unread > 0)
                            <span class="badge bg-primary rounded-pill">{{ $unread }}</span>
                        @endif
                    </a>
                @empty
                    <div class="p-3 text-muted small">No group channels yet. Create one to get started.</div>
                @endforelse
            </div>
        </div>

        <div class="col-md-8 col-lg-9 h-100 d-flex flex-column">
            @if ($active)
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-medium">{{ $active->name }}</div>
                        <div class="small text-muted">{{ $active->participants->count() }} members</div>
                    </div>
                </div>

                <div id="messageList" class="flex-grow-1 overflow-auto p-3" data-conversation-id="{{ $active->id }}">
                    @foreach ($active->messages as $message)
                        @include('backend.chat.message', ['message' => $message])
                    @endforeach
                </div>

                <div class="border-top">
                    <div id="replyPreviewBar" class="d-none px-3 pt-2 d-flex align-items-start justify-content-between bg-light">
                        <div class="border-start border-3 border-primary ps-2 small">
                            <div class="fw-medium" id="replyPreviewSender"></div>
                            <div class="text-muted text-truncate" id="replyPreviewBody" style="max-width:400px;"></div>
                        </div>
                        <button type="button" class="btn btn-sm btn-link text-muted" id="cancelReplyBtn"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <div class="p-3">
                        <form id="sendMessageForm" class="d-flex gap-2">
                            <input type="text" name="body" id="messageInput" class="form-control" placeholder="Type a message...">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i></button>
                        </form>
                    </div>
                </div>
            @else
                <div class="h-100 d-flex align-items-center justify-content-center text-muted">
                    <div class="text-center">
                        <i class="bi bi-chat-square-text display-4 d-block mb-2"></i>
                        Select a channel or create a new one to start chatting.
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="newGroupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.chat.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title">New Group Channel</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label small fw-medium">Channel Name</label>
                    <input type="text" name="name" class="form-control mb-3" placeholder="e.g. Marketing Team" required>

                    <label class="form-label small fw-medium">Members</label>
                    <select name="participants[]" class="form-select" multiple size="6" required>
                        @foreach ($admins as $a)
                            <option value="{{ $a->id }}">{{ $a->name }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Hold Ctrl / Cmd to select multiple.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Channel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('backend.chat.actions_scripts')

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.4.0-rc2/pusher.min.js"></script>
<script>
    // Conversations this admin can forward messages into (used by the
    // Forward modal in _actions_scripts.blade.php).
    window.__chatForwardTargets = @json($conversations->map(fn ($c) => ['id' => $c->id, 'name' => $c->name]));

    document.getElementById('channelSearch')?.addEventListener('input', function () {
        const term = this.value.toLowerCase();
        document.querySelectorAll('.channel-item').forEach(el => {
            el.style.display = el.textContent.toLowerCase().includes(term) ? '' : 'none';
        });
    });

    const list = document.getElementById('messageList');
    const form = document.getElementById('sendMessageForm');
    const input = document.getElementById('messageInput');

    function scrollToBottom() { if (list) list.scrollTop = list.scrollHeight; }
    scrollToBottom();

    function appendMessage(msg, isMine) {
        const wrap = document.createElement('div');
        wrap.className = `d-flex mb-3 ${isMine ? 'justify-content-end' : ''}`;
        wrap.innerHTML = `
            <div class="${isMine ? 'bg-primary text-white' : 'bg-light'} rounded p-2 px-3" style="max-width:70%;">
                ${!isMine ? `<div class="small fw-medium mb-1">${msg.sender_name}</div>` : ''}
                <div>${msg.body ?? ''}</div>
                <div class="small ${isMine ? 'text-white-50' : 'text-muted'} mt-1">${msg.created_at}</div>
            </div>`;
        list.appendChild(wrap);
        scrollToBottom();
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!input.value.trim()) return;

            fetch(`{{ url('admin/chat') }}/${list.dataset.conversationId}/send`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ body: input.value, reply_to_id: window.getReplyToId ? window.getReplyToId() : null })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        appendMessage(data.message, true);
                        input.value = '';
                        window.clearReplyState && window.clearReplyState();
                    }
                });
        });
    }

    if (window.Echo && list) {
        window.Echo.private(`chat.conversation.${list.dataset.conversationId}`)
            .listen('.message.sent', (e) => {
                if (e.sender_id != {{ auth('admin')->id() }}) {
                    appendMessage(e, false);
                }
            });
    }
</script>
@endsection