<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Concerns\InteractsWithChat;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * "Messages" — private 1:1 direct messages between two admins.
 * For group channels, see ChatController — same tables, different `type`.
 */
class MessageController extends Controller
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

    public function index(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('messages.view')) {
            abort(403, 'Sorry !! You are unauthorized to view Messages !');
        }

        // Resolve + mark the active conversation read FIRST, so the
        // unread counts computed below for the sidebar list already
        // reflect the read. (Marking read after fetching the list means
        // the list's pivot data was already loaded, so its unread count
        // would still show the stale, pre-read value.)
        $active = null;

        if ($request->filled('with')) {
            $other = Admin::findOrFail($request->with);
            $active = $this->findOrCreateDirectConversation($this->user, $other);
            $this->markRead($active, $this->user);
        } elseif ($request->filled('conversation')) {
            $active = ChatConversation::findOrFail($request->conversation);
            $this->authorizeParticipant($active, $this->user);
            $this->markRead($active, $this->user);
        }

        $conversations = $this->user->chatConversations()
            ->where('type', 'direct')
            ->with(['participants', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->get();

        if ($active) {
            $active->load(['participants', 'messages.sender']);
        }

        $admins = Admin::where('id', '!=', $this->user->id)->orderBy('first_name')->get();

        return response()
            ->view('backend.messages.index', compact('conversations', 'active', 'admins'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function send(Request $request, int $conversationId)
    {
        $conversation = $this->guardConversation($conversationId);

        $message = $this->sendMessage($request, $conversation, $this->user);

        return response()->json(['success' => true, 'message' => $this->formatMessage($message)]);
    }

    public function poll(Request $request, int $conversationId)
    {
        $conversation = $this->guardConversation($conversationId);

        $message = $this->sendPoll($request, $conversation, $this->user);

        return response()->json(['success' => true, 'message' => $this->formatMessage($message)]);
    }

    public function pollVote(Request $request, int $messageId)
    {
        if (is_null($this->user) || ! $this->user->can('messages.view')) {
            abort(403);
        }

        $request->validate(['option_id' => 'required|string']);

        $message = ChatMessage::findOrFail($messageId);
        $this->authorizeParticipant($message->conversation, $this->user);

        $message = $this->voteOnPoll($message, $this->user, $request->input('option_id'));

        return response()->json(['success' => true, 'message' => $this->formatMessage($message)]);
    }

    public function contact(Request $request, int $conversationId)
    {
        $conversation = $this->guardConversation($conversationId);

        $message = $this->sendContact($request, $conversation, $this->user);

        return response()->json(['success' => true, 'message' => $this->formatMessage($message)]);
    }

    public function event(Request $request, int $conversationId)
    {
        $conversation = $this->guardConversation($conversationId);

        $message = $this->sendEvent($request, $conversation, $this->user);

        return response()->json(['success' => true, 'message' => $this->formatMessage($message)]);
    }

    private function guardConversation(int $conversationId): ChatConversation
    {
        if (is_null($this->user) || ! $this->user->can('messages.view')) {
            abort(403);
        }

        $conversation = ChatConversation::findOrFail($conversationId);
        $this->authorizeParticipant($conversation, $this->user);

        return $conversation;
    }

    /**
     * Single source of truth for the JSON shape sent back to the browser
     * after any send* action — kept identical to what the Echo listener
     * receives from ChatMessageSent's broadcastWith(), so the same JS
     * renderer handles both "I just sent this" and "someone else sent
     * this" without branching.
     */
    private function formatMessage(ChatMessage $message): array
    {
        return [
            'id'               => $message->id,
            'type'             => $message->type,
            'body'             => $message->body,
            'attachment_url'   => $message->attachmentUrl(),
            'attachment_name'  => $message->attachment_name,
            'attachment_size'  => $message->humanFileSize(),
            'attachment_mime'  => $message->attachment_mime,
            'meta'             => $message->type === 'poll'
                ? $message->pollResults($this->user->id)
                : $message->meta,
            'sender_id'        => $message->admin_id,
            'sender_name'      => $this->user->name,
            'created_at'       => $message->created_at->timezone('Asia/Kolkata')->format('h:i A'),
        ];
    }
}