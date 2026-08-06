<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Concerns\InteractsWithChat;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ChatConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $active->load('participants');
            // Filtered per-viewer (respects this admin's own "Clear Chat"),
            // not a blanket eager-load of every message ever sent.
            $active->setRelation('messages', $active->visibleMessagesFor($this->user)->get());
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

        $memberIds = array_unique([...$validated['participants'], $this->user->id]);

        $now = now();
        $rows = array_map(fn ($adminId) => [
            'conversation_id' => $conversation->id,
            'admin_id'        => $adminId,
            'is_admin'        => $adminId === $this->user->id,
            'joined_at'       => $now,
            'created_at'      => $now,
            'updated_at'      => $now,
        ], $memberIds);

        \DB::table('chat_conversation_participants')->insert($rows);

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