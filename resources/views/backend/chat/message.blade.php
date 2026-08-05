@php
    $isMine = $message->admin_id === auth('admin')->id();
    $reactionSummary = $message->reactionSummary();
    $myReaction = $message->myReaction(auth('admin')->user());
    $isStarred = $message->isStarredBy(auth('admin')->user());
    $poll = $message->type === 'poll' ? $message->pollResults(auth('admin')->id()) : null;
@endphp
<div class="d-flex mt-5 mb-3 {{ $isMine ? 'justify-content-end' : '' }} chat-message-row" data-message-id="{{ $message->id }}" data-message-type="{{ $message->type }}">
    <div class="chat-bubble-wrap position-relative" style="max-width:70%;">

        <!-- Hover action toolbar -->
        <div class="chat-actions-toolbar position-absolute d-none gap-1 bg-white border rounded shadow-sm p-1"
             style="top:-34px; {{ $isMine ? 'right:0;' : 'left:0;' }} z-index:5;">
            <button type="button" class="btn btn-sm btn-light p-1 reply-btn" title="Reply"><i class="bi bi-reply"></i></button>
            <button type="button" class="btn btn-sm btn-light p-1 copy-btn" title="Copy"><i class="bi bi-clipboard"></i></button>
            <div class="dropdown">
                <button type="button" class="btn btn-sm btn-light p-1" data-bs-toggle="dropdown" title="More"><i class="bi bi-three-dots"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item small forward-btn" href="#"><i class="bi bi-arrow-90deg-right me-2"></i>Forward</a></li>
                    <li><a class="dropdown-item small pin-btn" href="#"><i class="bi bi-pin-angle me-2"></i>{{ $message->pinned_at ? 'Unpin' : 'Pin' }}</a></li>
                    <li><a class="dropdown-item small star-btn" href="#"><i class="bi bi-star{{ $isStarred ? '-fill text-warning' : '' }} me-2"></i>{{ $isStarred ? 'Unstar' : 'Star' }}</a></li>
                    <li><a class="dropdown-item small info-btn" href="#"><i class="bi bi-info-circle me-2"></i>Message Info</a></li>
                    @if ($isMine)
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item small text-danger delete-btn" href="#"><i class="bi bi-trash me-2"></i>Delete</a></li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="{{ $isMine ? 'bg-primary text-white' : 'bg-light' }} rounded p-2 px-3 position-relative">
            @if ($message->pinned_at)
                <div class="small {{ $isMine ? 'text-white-50' : 'text-muted' }} mb-1"><i class="bi bi-pin-angle-fill"></i> Pinned</div>
            @endif

            @if ($message->forwarded_from_id)
                <div class="small fst-italic {{ $isMine ? 'text-white-50' : 'text-muted' }} mb-1"><i class="bi bi-arrow-90deg-right"></i> Forwarded</div>
            @endif

            @if (! $isMine)
                <div class="small fw-medium mb-1">{{ $message->sender->name ?? 'Unknown' }}</div>
            @endif

            @if ($message->replyTo)
                <div class="border-start border-3 {{ $isMine ? 'border-white' : 'border-primary' }} ps-2 mb-2 small {{ $isMine ? 'text-white-50' : 'text-muted' }}">
                    <div class="fw-medium">{{ $message->replyTo->sender->name ?? 'Unknown' }}</div>
                    <div class="text-truncate" style="max-width:220px;">{{ \Illuminate\Support\Str::limit($message->replyTo->body, 60) }}</div>
                </div>
            @endif

            {{-- ============ TYPE-SPECIFIC BODY ============ --}}

            @if ($message->type === 'image')
                <a href="{{ $message->attachmentUrl() }}" target="_blank" class="d-block mb-1">
                    <img src="{{ $message->attachmentUrl() }}" alt="Image" class="rounded" style="max-width:260px;max-height:260px;object-fit:cover;">
                </a>

            @elseif ($message->type === 'video')
                <video controls class="rounded mb-1" style="max-width:280px;max-height:280px;">
                    <source src="{{ $message->attachmentUrl() }}" type="{{ $message->attachment_mime }}">
                </video>

            @elseif ($message->type === 'audio')
                <div class="d-flex align-items-center gap-2 mb-1" style="min-width:220px;">
                    <i class="bi bi-mic-fill {{ $isMine ? 'text-white' : 'text-primary' }}"></i>
                    <audio controls class="flex-grow-1" style="height:36px;">
                        <source src="{{ $message->attachmentUrl() }}" type="{{ $message->attachment_mime }}">
                    </audio>
                </div>

            @elseif ($message->type === 'document')
                <a href="{{ $message->attachmentUrl() }}" target="_blank" download
                   class="d-flex align-items-center gap-2 p-2 rounded {{ $isMine ? 'bg-primary border border-light border-opacity-25' : 'bg-white border' }} text-decoration-none {{ $isMine ? 'text-white' : 'text-dark' }} mb-1">
                    <i class="bi bi-file-earmark-arrow-down fs-4"></i>
                    <div class="overflow-hidden">
                        <div class="text-truncate small fw-medium" style="max-width:200px;">{{ $message->attachment_name }}</div>
                        <div class="small {{ $isMine ? 'text-white-50' : 'text-muted' }}">{{ $message->humanFileSize() }}</div>
                    </div>
                </a>

            @elseif ($message->type === 'contact')
                @php $c = $message->meta ?? []; @endphp
                <div class="d-flex align-items-center gap-2 p-2 rounded {{ $isMine ? 'bg-primary border border-light border-opacity-25' : 'bg-white border' }} mb-1" style="min-width:220px;">
                    <div class="rounded-circle bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-medium text-truncate">{{ $c['name'] ?? 'Contact' }}</div>
                        @if (!empty($c['phone']))
                            <a class="d-block small text-decoration-none {{ $isMine ? 'text-white-50' : 'text-muted' }}" href="tel:{{ $c['phone'] }}">{{ $c['phone'] }}</a>
                        @endif
                        @if (!empty($c['email']))
                            <a class="d-block small text-decoration-none {{ $isMine ? 'text-white-50' : 'text-muted' }}" href="mailto:{{ $c['email'] }}">{{ $c['email'] }}</a>
                        @endif
                    </div>
                </div>

            @elseif ($message->type === 'event')
                @php $e = $message->meta ?? []; @endphp
                <div class="rounded p-2 {{ $isMine ? 'bg-primary border border-light border-opacity-25' : 'bg-white border' }} mb-1" style="min-width:220px;">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-calendar-event"></i>
                        <span class="fw-medium">{{ $e['title'] ?? 'Event' }}</span>
                    </div>
                    @if (!empty($e['starts_at']))
                        <div class="small {{ $isMine ? 'text-white-50' : 'text-muted' }}">
                            {{ \Illuminate\Support\Carbon::parse($e['starts_at'])->timezone('Asia/Kolkata')->format('D, d M Y — h:i A') }}
                        </div>
                    @endif
                    @if (!empty($e['location']))
                        <div class="small {{ $isMine ? 'text-white-50' : 'text-muted' }}"><i class="bi bi-geo-alt"></i> {{ $e['location'] }}</div>
                    @endif
                    @if (!empty($e['description']))
                        <div class="small mt-1">{{ $e['description'] }}</div>
                    @endif
                </div>

            @elseif ($message->type === 'poll')
                <div class="rounded p-2 {{ $isMine ? 'bg-primary border border-light border-opacity-25' : 'bg-white border' }} mb-1 poll-block" style="min-width:240px;" data-message-id="{{ $message->id }}">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-bar-chart-fill"></i>
                        <span class="fw-medium">{{ $poll['question'] }}</span>
                    </div>
                    @foreach ($poll['options'] as $opt)
                        @php
                            $voted = $poll['multiple']
                                ? in_array($opt['id'], (array) ($poll['my_vote'] ?? []))
                                : $poll['my_vote'] === $opt['id'];
                        @endphp
                        <button type="button"
                                class="poll-option-btn btn btn-sm w-100 text-start mb-1 position-relative overflow-hidden {{ $isMine ? 'btn-light' : 'btn-outline-secondary' }} {{ $voted ? 'border-primary' : '' }}"
                                data-option-id="{{ $opt['id'] }}">
                            <span class="position-absolute top-0 start-0 h-100 bg-primary bg-opacity-25" style="width:{{ $opt['percentage'] }}%;z-index:0;"></span>
                            <span class="position-relative d-flex justify-content-between" style="z-index:1;">
                                <span>{{ $voted ? '✓ ' : '' }}{{ $opt['text'] }}</span>
                                <span class="text-muted">{{ $opt['percentage'] }}%</span>
                            </span>
                        </button>
                    @endforeach
                    <div class="small {{ $isMine ? 'text-white-50' : 'text-muted' }} mt-1">
                        {{ $poll['total_voters'] }} {{ Str::plural('vote', $poll['total_voters']) }}{{ $poll['multiple'] ? ' · Select one or more' : '' }}
                    </div>
                </div>
            @endif

            @if ($message->body)
                <div>{{ $message->body }}</div>
            @endif

            <div class="d-flex align-items-center gap-1 small {{ $isMine ? 'text-white-50' : 'text-muted' }} mt-1">
                <span>{{ $message->created_at->timezone('Asia/Kolkata')->format('h:i A') }}</span>
                @if ($message->edited_at)
                    <span>&middot; edited</span>
                @endif
                @if ($isMine)
                    <i class="bi {{ $message->isReadByOthers() ? 'bi-check2-all text-info' : 'bi-check2' }}"></i>
                @endif
                @if ($isStarred)
                    <i class="bi bi-star-fill text-warning ms-1" title="Starred"></i>
                @endif
            </div>
        </div>

        @if (! empty($reactionSummary))
            <div class="d-flex gap-1 mt-1 {{ $isMine ? 'justify-content-end' : '' }}">
                @foreach ($reactionSummary as $emoji => $count)
                    <span class="badge bg-white border text-dark small">{{ $emoji }} {{ $count }}</span>
                @endforeach
            </div>
        @endif
    </div>
</div>