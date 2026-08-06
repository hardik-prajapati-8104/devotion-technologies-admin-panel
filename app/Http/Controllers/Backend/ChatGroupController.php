<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Concerns\InteractsWithChat;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ChatConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Group settings panel actions — rename/avatar, membership, admin
 * roles, clear/exit/delete. Split out from ChatController so that
 * controller stays focused on listing + sending messages.
 */
class ChatGroupController extends Controller
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

    private function group(int $id): ChatConversation
    {
        $conversation = ChatConversation::where('type', 'group')->findOrFail($id);
        $this->authorizeParticipant($conversation, $this->user);

        return $conversation;
    }

    private function requireGroupAdmin(ChatConversation $conversation): void
    {
        if (! $conversation->isAdmin($this->user)) {
            abort(403, 'Only group admins can do that.');
        }
    }

    /**
     * Update the group name / avatar. Any participant can update the
     * name (matches most chat apps); only group admins can be more
     * strict if you'd rather lock renaming down — see requireGroupAdmin().
     */
    public function update(Request $request, int $id)
    {
        $conversation = $this->group($id);

        $validated = $request->validate([
            'name'   => 'required|string|max:100',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('avatar')) {
            if ($conversation->avatar) {
                Storage::disk('public')->delete($conversation->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('chat-avatars', 'public');
        } else {
            unset($validated['avatar']);
        }

        $conversation->update($validated);

        session()->flash('success', 'Group updated successfully !!');
        return back();
    }

    public function addMembers(Request $request, int $id)
    {
        $conversation = $this->group($id);
        $this->requireGroupAdmin($conversation);

        $validated = $request->validate([
            'participants'   => 'required|array|min:1',
            'participants.*' => 'exists:admins,id',
        ]);

        $existingIds = $conversation->participants()->pluck('admins.id')->all();
        $toAdd = array_diff($validated['participants'], $existingIds);

        if (! empty($toAdd)) {
            $now = now();
            $rows = array_map(fn ($adminId) => [
                'conversation_id' => $conversation->id,
                'admin_id'        => $adminId,
                'is_admin'        => false,
                'joined_at'       => $now,
                'created_at'      => $now,
                'updated_at'      => $now,
            ], $toAdd);

            DB::table('chat_conversation_participants')->insert($rows);
        }

        session()->flash('success', count($toAdd).' member(s) added !!');
        return back();
    }

    public function removeMember(int $id, int $adminId)
    {
        $conversation = $this->group($id);

        // Anyone can remove themselves (= leave); removing someone else
        // requires group-admin rights.
        if ($adminId !== $this->user->id) {
            $this->requireGroupAdmin($conversation);
        }

        DB::table('chat_conversation_participants')
            ->where('conversation_id', $conversation->id)
            ->where('admin_id', $adminId)
            ->delete();

        session()->flash('success', 'Member removed !!');
        return back();
    }

    public function toggleAdmin(int $id, int $adminId)
    {
        $conversation = $this->group($id);
        $this->requireGroupAdmin($conversation);

        $current = DB::table('chat_conversation_participants')
            ->where('conversation_id', $conversation->id)
            ->where('admin_id', $adminId)
            ->value('is_admin');

        DB::table('chat_conversation_participants')
            ->where('conversation_id', $conversation->id)
            ->where('admin_id', $adminId)
            ->update(['is_admin' => ! $current]);

        session()->flash('success', 'Member role updated !!');
        return back();
    }

        public function clearChat(int $id)
    {
        $conversation = $this->group($id);
    
        DB::table('chat_conversation_participants')
            ->where('conversation_id', $conversation->id)
            ->where('admin_id', $this->user->id)
            ->update(['cleared_at' => now()]);
    
        session()->flash('success', 'Chat cleared for you !!');
        return redirect()->route('admin.chat.index', ['conversation' => $conversation->id]);
    }

    /**
     * Deletes every message in the group. Simplified vs. WhatsApp's
     * "clear for me only" — this clears for everyone, since we don't
     * track per-user message visibility. Any participant can do this.
     */
 

    public function exit(int $id)
    {
        $conversation = $this->group($id);

        DB::table('chat_conversation_participants')
            ->where('conversation_id', $conversation->id)
            ->where('admin_id', $this->user->id)
            ->delete();

        // If no one is left, clean up the empty group entirely.
        $remaining = $conversation->participants()->count();
        if ($remaining === 0) {
            $conversation->delete();
        }

        session()->flash('success', 'You left the group.');
        return redirect()->route('admin.chat.index');
    }

    public function destroy(int $id)
    {
        $conversation = $this->group($id);
        $this->requireGroupAdmin($conversation);

        if ($conversation->avatar) {
            Storage::disk('public')->delete($conversation->avatar);
        }

        $conversation->delete(); // cascades to messages, participants, reactions, stars via FK

        session()->flash('success', 'Group deleted !!');
        return redirect()->route('admin.chat.index');
    }


    
}
