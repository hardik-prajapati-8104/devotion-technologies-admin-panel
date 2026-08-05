<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Concerns\InteractsWithChat;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ChatConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * "Internal Chat" — group channels shared by multiple admins
 * (team chat, project rooms, etc). For 1:1 direct messages, see
 * MessageController — same underlying tables, different `type`.
 */
class ChatController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('chat.view')) {
            abort(403, 'Sorry !! You are unauthorized to view Internal Chat !');
        }

        // Mark the active conversation read BEFORE fetching the sidebar
        // list, so its unread count reflects the read (see MessageController
        // for the same fix and full explanation).
        $active = null;

        if ($request->filled('conversation')) {
            $active = ChatConversation::findOrFail($request->conversation);
            $this->authorizeParticipant($active, $this->user);
            $this->markRead($active, $this->user);
        }

        $conversations = $this->user->chatConversations()
            ->where('type', 'group')
            ->with(['participants', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->get();

        if ($active) {
            $active->load(['participants', 'messages.sender']);
        }

        $admins = Admin::where('id', '!=', $this->user->id)->orderBy('first_name')->get();

        return view('backend.chat.index', compact('conversations', 'active', 'admins'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('chat.view')) {
            abort(403);
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:admins,id',
        ]);

        $conversation = ChatConversation::create([
            'type'       => 'group',
            'name'       => $validated['name'],
            'created_by' => $this->user->id,
        ]);

        $conversation->participants()->attach(array_unique([...$validated['participants'], $this->user->id]));

        return redirect()->route('admin.chat.index', ['conversation' => $conversation->id]);
    }

    public function send(Request $request, int $conversationId)
    {
        if (is_null($this->user) || ! $this->user->can('chat.view')) {
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