<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Concerns\InteractsWithChat;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ChatConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $active->load('participants');
            // Same per-viewer filtering as ChatController — respects this
            // admin's own "Clear Chat" without affecting the other person.
            $active->setRelation('messages', $active->visibleMessagesFor($this->user)->get());
        }

        $admins = Admin::where('id', '!=', $this->user->id)->orderBy('first_name')->get();

        return response()
            ->view('backend.messages.index', compact('conversations', 'active', 'admins'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function send(Request $request, int $conversationId)
    {
        if (is_null($this->user) || ! $this->user->can('messages.view')) {
            abort(403);
        }

        $conversation = ChatConversation::findOrFail($conversationId);
        $this->authorizeParticipant($conversation, $this->user);

        $message = $this->sendMessage($request, $conversation, $this->user);

        return response()->json([
            'success' => true,
            'message' => [
                'id'          => $message->id,
                'body'        => $message->body,
                'attachment'  => $message->attachment,
                'sender_id'   => $message->admin_id,
                'sender_name' => $this->user->name,
                'created_at'  => $message->created_at->format('h:i A'),
            ],
        ]);
    }
}