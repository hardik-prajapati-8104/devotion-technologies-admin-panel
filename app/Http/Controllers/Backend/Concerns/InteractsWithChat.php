<?php

namespace App\Http\Controllers\Backend\Concerns;

use App\Events\ChatMessageSent;
use App\Models\Admin;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Shared behaviour for ChatController (group channels) and
 * MessageController (1:1 direct messages) — both operate on the same
 * chat_conversations / chat_messages tables, just filtered by `type`
 * on the CONVERSATION (direct vs group). Not to be confused with the
 * MESSAGE-level `type` column added here (text/document/image/...).
 */
trait InteractsWithChat
{
    protected function authorizeParticipant(ChatConversation $conversation, Admin $admin): void
    {
        $isParticipant = DB::table('chat_conversation_participants')
            ->where('conversation_id', $conversation->id)
            ->where('admin_id', $admin->id)
            ->exists();

        if (! $isParticipant) {
            abort(403, 'You are not part of this conversation.');
        }
    }

    /**
     * Plain text messages, and any single-file attachment (document,
     * photo, video, or recorded/uploaded audio). The concrete `type` is
     * auto-detected from the file's mime unless `force_type` is passed
     * (used by the mic recorder, whose blob mime — audio/webm — is
     * already unambiguous, but forcing keeps intent explicit).
     */
    protected function sendMessage(Request $request, ChatConversation $conversation, Admin $sender): ChatMessage
    {
        $request->validate([
            'body'        => 'required_without:attachment|nullable|string|max:4000',
            'attachment'  => 'nullable|file|max:51200', // 50MB ceiling — tune to your storage/CDN plan
            'reply_to_id' => 'nullable|integer|exists:chat_messages,id',
            'force_type'  => 'nullable|in:document,image,video,audio',
            'duration'    => 'nullable|integer|min:0', // seconds, for audio notes
        ]);

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentMime = null;
        $attachmentSize = null;
        $type = 'text';
        $meta = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('chat-attachments', 'public');
            $attachmentName = $file->getClientOriginalName();
            $attachmentMime = $file->getMimeType();
            $attachmentSize = $file->getSize();
            $type = $this->resolveAttachmentType($attachmentMime, $request->input('force_type'));

            if ($type === 'audio' && $request->filled('duration')) {
                $meta = ['duration' => (int) $request->input('duration')];
            }
        }

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'admin_id'        => $sender->id,
            'body'            => $request->input('body'),
            'attachment'      => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_mime' => $attachmentMime,
            'attachment_size' => $attachmentSize,
            'type'            => $type,
            'meta'            => $meta,
            'reply_to_id'     => $request->input('reply_to_id'),
        ]);

        $this->afterMessageCreated($conversation, $sender, $message);

        return $message;
    }

    /**
     * Poll options are ordered strings from the form; blank rows are
     * dropped before we require >= 2 remain, so trailing empty inputs in
     * the UI (e.g. a "+ add option" row left blank) don't hard-fail.
     */
    protected function sendPoll(Request $request, ChatConversation $conversation, Admin $sender): ChatMessage
    {
        $request->validate([
            'question'  => 'required|string|max:255',
            'options'   => 'required|array|min:2|max:10',
            'options.*' => 'nullable|string|max:100',
            'multiple'  => 'nullable|boolean',
        ]);

        $options = collect($request->input('options'))
            ->map(fn ($opt) => trim((string) $opt))
            ->filter(fn ($opt) => $opt !== '')
            ->values()
            ->map(fn ($text, $i) => ['id' => 'opt_' . ($i + 1), 'text' => $text])
            ->all();

        if (count($options) < 2) {
            abort(422, 'A poll needs at least two non-empty options.');
        }

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'admin_id'        => $sender->id,
            'type'            => 'poll',
            'meta'            => [
                'question' => $request->input('question'),
                'options'  => $options,
                'multiple' => $request->boolean('multiple'),
                'votes'    => [],
            ],
        ]);

        $this->afterMessageCreated($conversation, $sender, $message);

        return $message;
    }

    /**
     * Casts (or, for multi-select polls, toggles) $voter's vote on
     * $message. Re-voting on a single-choice poll overwrites the
     * previous choice rather than stacking votes.
     */
    protected function voteOnPoll(ChatMessage $message, Admin $voter, string $optionId): ChatMessage
    {
        if ($message->type !== 'poll') {
            abort(422, 'This message is not a poll.');
        }

        $meta = $message->meta ?? [];
        $validIds = collect($meta['options'] ?? [])->pluck('id')->all();

        if (! in_array($optionId, $validIds, true)) {
            abort(422, 'Invalid poll option.');
        }

        $votes = $meta['votes'] ?? [];
        $key = (string) $voter->id;

        if (! empty($meta['multiple'])) {
            $current = array_values((array) ($votes[$key] ?? []));
            $votes[$key] = in_array($optionId, $current, true)
                ? array_values(array_diff($current, [$optionId]))
                : [...$current, $optionId];
        } else {
            $votes[$key] = $optionId;
        }

        $meta['votes'] = $votes;
        $message->update(['meta' => $meta]);
        $message = $message->fresh();

        // Reuses the message-sent channel so the poll bubble updates live
        // for everyone else in the conversation; the client distinguishes
        // "new message" vs "existing bubble, refresh its poll results" by
        // checking whether that message id is already in the DOM.
        broadcast(new ChatMessageSent($message))->toOthers();

        return $message;
    }

    protected function sendContact(Request $request, ChatConversation $conversation, Admin $sender): ChatMessage
    {
        $request->validate([
            'name'  => 'required|string|max:150',
            'phone' => 'nullable|string|max:30|required_without:email',
            'email' => 'nullable|email|max:150|required_without:phone',
        ]);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'admin_id'        => $sender->id,
            'type'            => 'contact',
            'meta'            => $request->only('name', 'phone', 'email'),
        ]);

        $this->afterMessageCreated($conversation, $sender, $message);

        return $message;
    }

    protected function sendEvent(Request $request, ChatConversation $conversation, Admin $sender): ChatMessage
    {
        $request->validate([
            'title'       => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'starts_at'   => 'required|date',
            'location'    => 'nullable|string|max:255',
        ]);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'admin_id'        => $sender->id,
            'type'            => 'event',
            'meta'            => $request->only('title', 'description', 'starts_at', 'location'),
        ]);

        $this->afterMessageCreated($conversation, $sender, $message);

        return $message;
    }

    /**
     * Marks everything in the conversation as read for this admin.
     *
     * Deliberately raw DB here rather than the Eloquent relation/pivot —
     * this table is written and read from several places (sidebar badge,
     * this method, event broadcasts) and a plain UPDATE removes any
     * possibility of stale relation state or pivot-key mismatch causing
     * a silent no-op.
     */
    protected function markRead(ChatConversation $conversation, Admin $admin): void
    {
        $lastMessageId = ChatMessage::where('conversation_id', $conversation->id)->max('id');

        if (! $lastMessageId) {
            return;
        }

        $updated = DB::table('chat_conversation_participants')
            ->where('conversation_id', $conversation->id)
            ->where('admin_id', $admin->id)
            ->update(['last_read_message_id' => $lastMessageId, 'updated_at' => now()]);

        // Defensive: if the participant row didn't exist for some reason
        // (e.g. data created before a schema fix), create it instead of
        // silently doing nothing.
        if (! $updated) {
            DB::table('chat_conversation_participants')->updateOrInsert(
                ['conversation_id' => $conversation->id, 'admin_id' => $admin->id],
                ['last_read_message_id' => $lastMessageId, 'joined_at' => now(), 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }

    protected function findOrCreateDirectConversation(Admin $me, Admin $other): ChatConversation
    {
        $existingId = DB::table('chat_conversations')
            ->where('type', 'direct')
            ->whereIn('id', function ($q) use ($me) {
                $q->select('conversation_id')->from('chat_conversation_participants')->where('admin_id', $me->id);
            })
            ->whereIn('id', function ($q) use ($other) {
                $q->select('conversation_id')->from('chat_conversation_participants')->where('admin_id', $other->id);
            })
            ->value('id');

        if ($existingId) {
            return ChatConversation::findOrFail($existingId);
        }

        $conversation = ChatConversation::create([
            'type'       => 'direct',
            'created_by' => $me->id,
        ]);

        $now = now();
        DB::table('chat_conversation_participants')->insert([
            ['conversation_id' => $conversation->id, 'admin_id' => $me->id, 'joined_at' => $now, 'created_at' => $now, 'updated_at' => $now],
            ['conversation_id' => $conversation->id, 'admin_id' => $other->id, 'joined_at' => $now, 'created_at' => $now, 'updated_at' => $now],
        ]);

        return $conversation;
    }

    private function resolveAttachmentType(?string $mime, ?string $forced = null): string
    {
        if ($forced) {
            return $forced;
        }

        return match (true) {
            Str::startsWith($mime, 'image/') => 'image',
            Str::startsWith($mime, 'video/') => 'video',
            Str::startsWith($mime, 'audio/') => 'audio',
            default => 'document',
        };
    }

    /**
     * Shared tail of every send* method: bump conversation ordering,
     * mark the sender's own copy read, broadcast to everyone else.
     */
    private function afterMessageCreated(ChatConversation $conversation, Admin $sender, ChatMessage $message): void
    {
        $conversation->update(['last_message_at' => now()]);

        DB::table('chat_conversation_participants')
            ->where('conversation_id', $conversation->id)
            ->where('admin_id', $sender->id)
            ->update(['last_read_message_id' => $message->id, 'updated_at' => now()]);

        broadcast(new ChatMessageSent($message))->toOthers();
    }
}