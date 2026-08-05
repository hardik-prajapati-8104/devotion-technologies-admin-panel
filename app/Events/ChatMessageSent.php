<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ChatMessage $message)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.conversation.'.$this->message->conversation_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $this->message->loadMissing(['sender', 'replyTo.sender', 'forwardedFrom.sender']);

        return [
            'id'               => $this->message->id,
            'conversation_id'  => $this->message->conversation_id,
            'type'             => $this->message->type,
            'body'             => $this->message->body,
            'attachment'       => $this->message->attachment,
            'attachment_url'   => $this->message->attachmentUrl(),
            'attachment_name'  => $this->message->attachment_name,
            'attachment_mime'  => $this->message->attachment_mime,
            'attachment_size'  => $this->message->humanFileSize(),
            // No single "current viewer" to compute my_vote against for a
            // broadcast (it fans out to everyone) — the client already
            // treats this as "someone else's" message, so my_vote is left
            // null; the recipient's own vote (if any) will show correctly
            // next time they load/refresh the conversation.
            'meta'             => $this->message->type === 'poll'
                ? $this->message->pollResults(null)
                : $this->message->meta,
            'sender_id'        => $this->message->admin_id,
            'sender_name'      => $this->message->sender->name ?? 'Unknown',
            'created_at'       => $this->message->created_at->toIso8601String(),
            'created_at_display' => $this->message->created_at->timezone('Asia/Kolkata')->format('h:i A'),
            'reply_to'         => $this->message->replyTo ? [
                'sender' => $this->message->replyTo->sender->name ?? 'Unknown',
                'body'   => \Str::limit($this->message->replyTo->body, 80),
            ] : null,
            'forwarded'        => (bool) $this->message->forwarded_from_id,
        ];
    }
}