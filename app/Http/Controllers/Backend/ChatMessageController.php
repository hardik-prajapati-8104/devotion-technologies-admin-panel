<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Concerns\InteractsWithChat;
use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\ChatMessageReaction;
use App\Models\ChatMessageStar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Per-message actions (react / pin / star / info / forward / delete)
 * shared by both Internal Chat and Messages — a message's parent
 * conversation determines which permission gate applies, so this one
 * controller serves both contexts instead of duplicating six actions
 * across ChatController and MessageController.
 */
class ChatMessageController extends Controller
{
    use InteractsWithChat;

    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    private function authorizeOnMessage(ChatMessage $message): ChatConversation
    {
        if (! $this->user->can('chat.view') && ! $this->user->can('messages.view')) {
            abort(403);
        }

        $conversation = ChatConversation::findOrFail($message->conversation_id);
        $this->authorizeParticipant($conversation, $this->user);

        return $conversation;
    }

    /**
     * Full metadata for the "Message Info" panel: timestamps, reply/forward
     * context, and — for direct chats — whether the other person has read it.
     */
    public function info(ChatMessage $message)
    {
        $this->authorizeOnMessage($message);
        $message->load(['sender', 'replyTo.sender', 'forwardedFrom.sender', 'reactions.admin']);

        return response()->json([
            'id'          => $message->id,
            'sender'      => $message->sender->name ?? 'Unknown',
            'body'        => $message->body,
            'sent_at'     => $message->created_at->format('d M Y, h:i:s A'),
            'edited_at'   => $message->edited_at?->format('d M Y, h:i:s A'),
            'is_read'     => $message->isReadByOthers(),
            'reply_to'    => $message->replyTo ? [
                'sender' => $message->replyTo->sender->name ?? 'Unknown',
                'body'   => \Str::limit($message->replyTo->body, 80),
            ] : null,
            'forwarded_from' => $message->forwardedFrom ? [
                'sender' => $message->forwardedFrom->sender->name ?? 'Unknown',
            ] : null,
            'reactions' => $message->reactions->map(fn ($r) => [
                'admin' => $r->admin->name ?? 'Unknown',
                'emoji' => $r->emoji,
            ]),
        ]);
    }

    /**
     * Toggle a reaction: same emoji again removes it, a different emoji
     * replaces it (one reaction per admin per message).
     */
    public function react(Request $request, ChatMessage $message)
    {
        $this->authorizeOnMessage($message);

        $validated = $request->validate([
            'emoji' => 'required|string|max:8',
        ]);

        $existing = ChatMessageReaction::where('message_id', $message->id)
            ->where('admin_id', $this->user->id)
            ->first();

        if ($existing && $existing->emoji === $validated['emoji']) {
            $existing->delete();
            $removed = true;
        } else {
            ChatMessageReaction::updateOrCreate(
                ['message_id' => $message->id, 'admin_id' => $this->user->id],
                ['emoji' => $validated['emoji']]
            );
            $removed = false;
        }

        $message->load('reactions');

        return response()->json([
            'success'   => true,
            'removed'   => $removed,
            'summary'   => $message->reactionSummary(),
        ]);
    }

    public function togglePin(ChatMessage $message)
    {
        $this->authorizeOnMessage($message);

        if ($message->pinned_at) {
            $message->update(['pinned_at' => null, 'pinned_by' => null]);
            $pinned = false;
        } else {
            $message->update(['pinned_at' => now(), 'pinned_by' => $this->user->id]);
            $pinned = true;
        }

        return response()->json(['success' => true, 'pinned' => $pinned]);
    }

    public function toggleStar(ChatMessage $message)
    {
        $this->authorizeOnMessage($message);

        $star = ChatMessageStar::where('message_id', $message->id)->where('admin_id', $this->user->id)->first();

        if ($star) {
            $star->delete();
            $starred = false;
        } else {
            ChatMessageStar::create(['message_id' => $message->id, 'admin_id' => $this->user->id]);
            $starred = true;
        }

        return response()->json(['success' => true, 'starred' => $starred]);
    }

    /**
     * Forward a message into one or more other conversations the admin
     * is a participant of. Creates independent copies rather than moving
     * the original, and tags each copy with forwarded_from_id.
     */
    public function forward(Request $request, ChatMessage $message)
    {
        $this->authorizeOnMessage($message);

        $validated = $request->validate([
            'conversation_ids'   => 'required|array|min:1',
            'conversation_ids.*' => 'integer|exists:chat_conversations,id',
        ]);

        $forwarded = [];

        foreach ($validated['conversation_ids'] as $conversationId) {
            $target = ChatConversation::findOrFail($conversationId);
            $this->authorizeParticipant($target, $this->user);

            $copy = ChatMessage::create([
                'conversation_id'   => $target->id,
                'admin_id'          => $this->user->id,
                'body'              => $message->body,
                'attachment'        => $message->attachment,
                'forwarded_from_id' => $message->id,
            ]);

            $target->update(['last_message_at' => now()]);

            DB::table('chat_conversation_participants')
                ->where('conversation_id', $target->id)
                ->where('admin_id', $this->user->id)
                ->update(['last_read_message_id' => $copy->id]);

            broadcast(new \App\Events\ChatMessageSent($copy))->toOthers();

            $forwarded[] = $copy->id;
        }

        return response()->json(['success' => true, 'forwarded_message_ids' => $forwarded]);
    }

    /**
     * Soft-delete — "delete for me" isn't distinguished from "delete for
     * everyone" here; only the sender can delete, and it disappears for
     * all participants (simpler, matches most internal-tool chat UX).
     */
    public function destroy(ChatMessage $message)
    {
        $this->authorizeOnMessage($message);

        if ($message->admin_id !== $this->user->id && ! $this->user->hasRole('superadmin')) {
            abort(403, 'You can only delete your own messages.');
        }

        $message->delete();

        return response()->json(['success' => true]);
    }
}
