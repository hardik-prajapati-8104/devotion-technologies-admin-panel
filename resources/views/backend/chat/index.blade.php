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
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0 overflow-hidden" style="width:38px;height:38px;">
                            @if ($c->avatar_url)
                                <img src="{{ $c->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <i class="bi bi-hash"></i>
                            @endif
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
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between" style="cursor:pointer;" data-bs-toggle="offcanvas" data-bs-target="#groupInfoOffcanvas">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0 overflow-hidden" style="width:38px;height:38px;">
                            @if ($active->avatar_url)
                                <img src="{{ $active->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <i class="bi bi-hash"></i>
                            @endif
                        </div>
                        <div>
                            <div class="fw-medium">{{ $active->name }}</div>
                            <div class="small text-muted">{{ $active->participants->count() }} members &middot; tap for group info</div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-muted"></i>
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

<!-- New group modal -->
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

@if ($active)
    @php $isGroupAdmin = $active->isAdmin(auth('admin')->user()); @endphp
    @php $memberIds = $active->participants->pluck('id')->all(); @endphp
    @php $nonMembers = $admins->reject(fn ($a) => in_array($a->id, $memberIds)); @endphp

    <!-- Group Info offcanvas (the "click group name" panel) -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="groupInfoOffcanvas" style="width:380px;">
        <div class="offcanvas-header border-bottom">
            <h6 class="offcanvas-title">Group Info</h6>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">

            <!-- Avatar + name -->
            <div class="p-4 text-center border-bottom">
                <form action="{{ route('admin.chat.groups.update', $active->id) }}" method="POST" enctype="multipart/form-data" id="groupAvatarForm">
                    @csrf
                    @method('PUT')
                    <label for="groupAvatarInput" style="cursor:pointer;">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mx-auto overflow-hidden position-relative" style="width:96px;height:96px;">
                            @if ($active->avatar_url)
                                <img src="{{ $active->avatar_url }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <i class="bi bi-people fs-1"></i>
                            @endif
                            <div class="position-absolute bottom-0 end-0 bg-dark text-white rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;">
                                <i class="bi bi-camera small"></i>
                            </div>
                        </div>
                    </label>
                    <input type="file" name="avatar" id="groupAvatarInput" accept="image/png,image/jpeg,image/webp" class="d-none" onchange="document.getElementById('groupAvatarForm').submit()">

                    <div class="mt-3">
                        @if ($isGroupAdmin)
                            <input type="text" name="name" form="groupAvatarForm" value="{{ $active->name }}" class="form-control form-control-sm text-center fw-medium border-0 bg-transparent">
                        @else
                            <div class="fw-medium">{{ $active->name }}</div>
                        @endif
                        <div class="small text-muted">Group &middot; {{ $active->participants->count() }} members</div>
                    </div>
                    @if ($isGroupAdmin)
                        <button type="submit" class="btn btn-sm btn-outline-primary mt-2"><i class="bi bi-check2 me-1"></i>Save Name</button>
                    @endif
                </form>
            </div>

            <!-- Members -->
            <div class="p-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 small text-uppercase text-muted">{{ $active->participants->count() }} Members</h6>
                    @if ($isGroupAdmin && $nonMembers->isNotEmpty())
                        <button type="button" class="btn btn-sm btn-link p-0" data-bs-toggle="modal" data-bs-target="#addMembersModal">
                            <i class="bi bi-person-plus"></i> Add
                        </button>
                    @endif
                </div>

                @foreach ($active->participants as $member)
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center flex-shrink-0" style="width:34px;height:34px;">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="small fw-medium">{{ $member->name }} @if ($member->id === auth('admin')->id()) <span class="text-muted">(You)</span> @endif</div>
                                @if ($member->pivot->is_admin)
                                    <span class="badge bg-light text-dark border small">Group Admin</span>
                                @endif
                            </div>
                        </div>

                        @if ($isGroupAdmin && $member->id !== auth('admin')->id())
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted p-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <form action="{{ route('admin.chat.groups.toggle-admin', [$active->id, $member->id]) }}" method="POST">
                                            @csrf @method('PUT')
                                            <button type="submit" class="dropdown-item small">
                                                {{ $member->pivot->is_admin ? 'Dismiss as admin' : 'Make group admin' }}
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.chat.groups.remove-member', [$active->id, $member->id]) }}" method="POST"
                                              onsubmit="return confirm('Remove {{ $member->name }} from this group?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item small text-danger">Remove from group</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Actions -->
            <div class="p-3">
                <form action="{{ route('admin.chat.groups.clear', $active->id) }}" method="POST" onsubmit="return confirm('Clear all messages in this group? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-secondary w-100 mb-2 text-start"><i class="bi bi-trash3 me-2"></i>Clear Chat</button>
                </form>

                <form action="{{ route('admin.chat.groups.exit', $active->id) }}" method="POST" onsubmit="return confirm('Leave this group?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-secondary w-100 mb-2 text-start"><i class="bi bi-box-arrow-right me-2"></i>Exit Group</button>
                </form>

                @if ($isGroupAdmin)
                    <form action="{{ route('admin.chat.groups.destroy', $active->id) }}" method="POST" onsubmit="return confirm('Delete this group for everyone? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 text-start"><i class="bi bi-exclamation-triangle me-2"></i>Delete Group</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Add members modal -->
    <div class="modal fade" id="addMembersModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.chat.groups.add-members', $active->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title">Add Members</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <select name="participants[]" class="form-select" multiple size="6" required>
                            @foreach ($nonMembers as $a)
                                <option value="{{ $a->id }}">{{ $a->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@include('backend.chat.actions_scripts')

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.4.0-rc2/pusher.min.js"></script>
<script>
    @php
        $chatForwardTargets = $conversations->map(function ($c) {
            return ['id' => $c->id, 'name' => $c->name];
        })->values();
    @endphp
    window.__chatForwardTargets = @json($chatForwardTargets);

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