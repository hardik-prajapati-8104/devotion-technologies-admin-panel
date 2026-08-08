<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskAttachmentController extends Controller
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

        $request->validate(['file' => 'required|file|max:10240']);

        $file = $request->file('file');
        $path = $file->store('task-attachments', 'public');

        $attachment = TaskAttachment::create([
            'task_id'       => $task->id,
            'admin_id'      => $this->user->id,
            'original_name' => $file->getClientOriginalName(),
            'file_path'     => $path,
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
        ]);

        return response()->json(['success' => true, 'attachment' => [
            'id'            => $attachment->id,
            'original_name' => $attachment->original_name,
            'human_size'    => $attachment->human_size,
            'url'           => $attachment->url,
            'uploader'      => $this->user->name,
            'is_image'      => $attachment->isImage(),
        ]]);
    }

    public function destroy(int $id)
    {
        $attachment = TaskAttachment::findOrFail($id);
        if (! $attachment->task->board->isMember($this->user)) abort(403);

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return response()->json(['success' => true]);
    }
}
