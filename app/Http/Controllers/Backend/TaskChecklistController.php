<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskChecklist;
use App\Models\TaskChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskChecklistController extends Controller
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

        $validated = $request->validate(['title' => 'nullable|string|max:100']);
        $position = $task->checklists()->max('position') + 1;

        $checklist = TaskChecklist::create([
            'task_id'  => $task->id,
            'title'    => $validated['title'] ?: 'Checklist',
            'position' => $position,
        ]);

        return response()->json(['success' => true, 'checklist' => $checklist->load('items')]);
    }

    public function destroy(int $id)
    {
        $checklist = TaskChecklist::findOrFail($id);
        if (! $checklist->task->board->isMember($this->user)) abort(403);

        $checklist->delete();

        return response()->json(['success' => true]);
    }

    public function storeItem(Request $request, int $checklistId)
    {
        $checklist = TaskChecklist::findOrFail($checklistId);
        if (! $checklist->task->board->isMember($this->user)) abort(403);

        $validated = $request->validate(['title' => 'required|string|max:200']);
        $position = $checklist->items()->max('position') + 1;

        $item = TaskChecklistItem::create([
            'checklist_id' => $checklist->id,
            'title'        => $validated['title'],
            'position'     => $position,
        ]);

        return response()->json(['success' => true, 'item' => $item]);
    }

    public function toggleItem(int $itemId)
    {
        $item = TaskChecklistItem::findOrFail($itemId);
        if (! $item->checklist->task->board->isMember($this->user)) abort(403);

        $item->update(['is_completed' => ! $item->is_completed]);

        return response()->json(['success' => true, 'is_completed' => $item->is_completed]);
    }

    public function destroyItem(int $itemId)
    {
        $item = TaskChecklistItem::findOrFail($itemId);
        if (! $item->checklist->task->board->isMember($this->user)) abort(403);

        $item->delete();

        return response()->json(['success' => true]);
    }
}
