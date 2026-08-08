<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskCommentController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function store(Request $request, int $taskId)
    {
        $task = Task::findOrFail($taskId);
        if (! $task->board->isMember($this->user)) abort(403);

        $validated = $request->validate(['body' => 'required|string|max:2000']);

        $comment = TaskComment::create([
            'task_id'  => $task->id,
            'admin_id' => $this->user->id,
            'body'     => $validated['body'],
        ]);

        return response()->json(['success' => true, 'comment' => [
            'id'         => $comment->id,
            'body'       => $comment->body,
            'admin_name' => $this->user->name,
            'created_at' => $comment->created_at->format('d M Y, h:i A'),
            'is_mine'    => true,
        ]]);
    }

    public function destroy(int $id)
    {
        $comment = TaskComment::findOrFail($id);
        if ($comment->admin_id !== $this->user->id && ! $this->user->hasRole('superadmin')) {
            abort(403);
        }

        $comment->delete();

        return response()->json(['success' => true]);
    }
}
