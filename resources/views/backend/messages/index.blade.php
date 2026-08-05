@extends('backend.layouts.master')

@section('title', 'Messages')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Messages</li>
        </ol>
    </nav>
    <div>
        <h4>Messages</h4>
        <p class="subtitle">Private one-to-one conversations with other admins.</p>
    </div>
@endsection

@section('admin-content')

<div class="card" style="height:75vh;">
    <div class="row g-0 h-100">
        <div class="col-md-4 col-lg-3 border-end h-100 d-flex flex-column">
            <div class="p-3 border-bottom">
                <select id="startConversation" class="form-select form-select-sm">
                    <option value="">+ Start a new message...</option>
                    @foreach ($admins as $a)
                        <option value="{{ $a->id }}">{{ $a->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="overflow-auto flex-grow-1">
                @forelse ($conversations as $c)
                    @php $other = $c->participants->firstWhere('id', '!=', auth('admin')->id()); @endphp
                    <a href="{{ route('admin.messages.index', ['conversation' => $c->id]) }}"
                       class="d-flex align-items-center gap-2 p-3 text-decoration-none border-bottom {{ $active && $active->id === $c->id ? 'bg-light' : '' }}">
                        <div class="rounded-circle flex-shrink-0 overflow-hidden" style="width:38px;height:38px;">
                            <img
                                src="{{ $other?->profile_image
                                        ? asset('storage/app/public/'.$other->profile_image)
                                        : 'https://ui-avatars.com/api/?background=aa8038&color=fff&name='.urlencode($other->name ?? 'Admin') }}"
                                alt="{{ $other->name ?? 'Admin' }}"
                                class="w-100 h-100"
                                style="object-fit:cover;"
                            >
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-medium text-dark text-truncate">{{ $other->name ?? 'Unknown' }}</div>
                            <div class="small text-muted text-truncate">{{ $c->latestMessage->body ?? 'No messages yet' }}</div>
                        </div>
                        @php $unread = $c->unreadCountFor(auth('admin')->user()); @endphp
                        @if ($unread > 0)
                            <span class="badge bg-primary rounded-pill">{{ $unread }}</span>
                        @endif
                    </a>
                @empty
                    <div class="p-3 text-muted small">No conversations yet. Pick someone above to start.</div>
                @endforelse
            </div>
        </div>

        <div class="col-md-8 col-lg-9 h-100 d-flex flex-column">
            @if ($active)
                @php $other = $active->participants->firstWhere('id', '!=', auth('admin')->id()); @endphp
                <div class="p-3 border-bottom d-flex align-items-center gap-2">
                    <div class="rounded-circle flex-shrink-0 overflow-hidden" style="width:32px;height:32px;">
                        <img
                            src="{{ $other?->profile_image
                                    ? url('storage/app/public/'.$other->profile_image)
                                    : 'https://ui-avatars.com/api/?background=aa8038&color=fff&name='.urlencode($other->name ?? 'Admin') }}"
                            alt="{{ $other->name ?? 'Admin' }}"
                            class="w-100 h-100"
                            style="object-fit:cover;"
                        >
                    </div>
                    <div class="fw-medium">{{ $other->name ?? 'Unknown' }}</div>
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

                    @include('backend.chat.attachment_menu')

                    <div class="p-3 pt-2">
                        <form id="sendMessageForm" class="d-flex gap-2">
                            <input type="text" name="body" id="messageInput" class="form-control" placeholder="Type a message...">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i></button>
                        </form>
                    </div>
                </div>
            @else
                <div class="h-100 d-flex align-items-center justify-content-center text-muted">
                    <div class="text-center">
                        <i class="bi bi-envelope display-4 d-block mb-2"></i>
                        Pick someone from the list to start a conversation.
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@include('backend.chat.actions_scripts')

@endsection
@php
    // Precomputed here rather than inline inside @json(...) below —
    // Blade's @json directive parser can choke on multi-line closures
    // with ->, ??, and array literals even though the PHP itself is valid.
    $chatForwardTargets = $conversations->map(function ($c) {
        $other = $c->participants->firstWhere('id', '!=', auth('admin')->id());
        return ['id' => $c->id, 'name' => $other->name ?? 'Unknown'];
    })->values();
@endphp
@section('scripts')
<script>
    window.__chatForwardTargets = @json($chatForwardTargets);

    document.getElementById('startConversation')?.addEventListener('change', function () {
        if (this.value) {
            window.location = `{{ route('admin.messages.index') }}?with=${this.value}`;
        }
    });

    const list = document.getElementById('messageList');
    const form = document.getElementById('sendMessageForm');
    const input = document.getElementById('messageInput');

    function scrollToBottom() { if (list) list.scrollTop = list.scrollHeight; }
    scrollToBottom();

    // Plain text sends go through the same shared renderer (window.renderChatBubble,
    // defined in backend.chat.attachment_menu) that documents/photos/polls/etc. use —
    // one rendering path for every message type, whether it's mine or incoming.
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!input.value.trim()) return;

            fetch(`{{ url('admin/messages') }}/${list.dataset.conversationId}/send`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ body: input.value, reply_to_id: window.getReplyToId ? window.getReplyToId() : null })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.renderChatBubble(data.message, true);
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
                    window.renderChatBubble(e, false);
                }
            });
    }
</script>
@endsection