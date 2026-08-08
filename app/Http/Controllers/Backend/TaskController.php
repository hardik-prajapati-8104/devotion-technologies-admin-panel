<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Task;
use App\Models\TaskBoard;
use App\Models\TaskColumn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    private function authorizeTask(Task $task): void
    {
        if (! $task->board->isMember($this->user)) {
            abort(403);
        }
    }

    public function store(Request $request, int $columnId)
    {
        $column = TaskColumn::findOrFail($columnId);
        $board = $column->board;

        if (! $board->isMember($this->user) || ! $this->user->can('tasks.create')) {
            abort(403);
        }

        $validated = $request->validate(['title' => 'required|string|max:200']);

        $position = $column->tasks()->max('position') + 1;

        $task = Task::create([
            'board_id'   => $board->id,
            'column_id'  => $column->id,
            'title'      => $validated['title'],
            'position'   => $position,
            'created_by' => $this->user->id,
        ]);

        ActivityLog::record('created', 'Task', $task->id, "Created task \"{$task->title}\".");

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'task' => $task]);
        }

        return back();
    }

    /**
     * Returns the full card-detail modal as rendered HTML, for the
     * kanban board's AJAX card-open interaction.
     */
    public function card(int $id)
    {
        $task = Task::with([
            'assignees', 'labels', 'checklists.items', 'attachments.uploader',
            'comments.admin', 'board.labels', 'board.members',
        ])->findOrFail($id);

        $this->authorizeTask($task);

        $boardMembers = $task->board->members;

        return view('backend.tasks.boards.card-modal-content', compact('task', 'boardMembers'));
    }

    public function update(Request $request, int $id)
    {
        $task = Task::findOrFail($id);
        $this->authorizeTask($task);
        if (! $this->user->can('tasks.edit')) abort(403);

        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:200',
            'description' => 'nullable|string',
            'priority'    => 'sometimes|in:low,medium,high,urgent',
            'due_date'    => 'nullable|date',
        ]);

        $task->update($validated);

        return response()->json(['success' => true]);
    }

    public function toggleComplete(int $id)
    {
        $task = Task::findOrFail($id);
        $this->authorizeTask($task);

        $task->update(['completed_at' => $task->completed_at ? null : now()]);

        return response()->json(['success' => true, 'completed' => (bool) $task->completed_at]);
    }

    /**
     * Drag-and-drop move: new column + new position, and everything
     * else in both the source and destination columns gets reflowed.
     */
    public function move(Request $request, int $id)
    {
        $task = Task::findOrFail($id);
        $this->authorizeTask($task);

        $validated = $request->validate([
            'column_id' => 'required|exists:task_columns,id',
            'position'  => 'required|integer|min:0',
        ]);

        $oldColumnId = $task->column_id;
        $task->update(['column_id' => $validated['column_id'], 'position' => $validated['position']]);

        // Reflow positions in the destination column (and the source
        // column too, if it changed) so there are no gaps/duplicates.
        foreach (array_unique([$oldColumnId, $validated['column_id']]) as $columnId) {
            TaskColumn::find($columnId)?->tasks()->orderBy('position')->get()
                ->values()->each(fn ($t, $i) => $t->update(['position' => $i]));
        }

        return response()->json(['success' => true]);
    }

    public function toggleAssignee(Request $request, int $id)
    {
        $task = Task::findOrFail($id);
        $this->authorizeTask($task);

        $validated = $request->validate(['admin_id' => 'required|exists:admins,id']);

        if ($task->assignees()->where('admin_id', $validated['admin_id'])->exists()) {
            $task->assignees()->detach($validated['admin_id']);
            $assigned = false;
        } else {
            $task->assignees()->attach($validated['admin_id']);
            $assigned = true;
        }

        return response()->json(['success' => true, 'assigned' => $assigned]);
    }

    public function toggleLabel(Request $request, int $id)
    {
        $task = Task::findOrFail($id);
        $this->authorizeTask($task);

        $validated = $request->validate(['label_id' => 'required|exists:task_labels,id']);

        if ($task->labels()->where('label_id', $validated['label_id'])->exists()) {
            $task->labels()->detach($validated['label_id']);
            $attached = false;
        } else {
            $task->labels()->attach($validated['label_id']);
            $attached = true;
        }

        return response()->json(['success' => true, 'attached' => $attached]);
    }

    public function destroy(int $id)
    {
        $task = Task::findOrFail($id);
        $this->authorizeTask($task);
        if (! $this->user->can('tasks.delete')) abort(403);

        $title = $task->title;
        $task->delete();

        ActivityLog::record('deleted', 'Task', $id, "Deleted task \"{$title}\".");

        return response()->json(['success' => true]);
    }
}
