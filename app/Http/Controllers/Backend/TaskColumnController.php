<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\TaskBoard;
use App\Models\TaskColumn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskColumnController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    private function authorizeBoard(TaskBoard $board): void
    {
        if (! $board->isMember($this->user)) {
            abort(403);
        }
    }

    public function store(Request $request, int $boardId)
    {
        $board = TaskBoard::findOrFail($boardId);
        $this->authorizeBoard($board);

        $validated = $request->validate(['name' => 'required|string|max:100']);

        $position = $board->columns()->max('position') + 1;

        $column = TaskColumn::create([
            'board_id' => $board->id,
            'name'     => $validated['name'],
            'position' => $position,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'column' => $column]);
        }

        return back();
    }

    public function update(Request $request, int $id)
    {
        $column = TaskColumn::findOrFail($id);
        $this->authorizeBoard($column->board);

        $validated = $request->validate([
            'name'  => 'sometimes|string|max:100',
            'color' => 'sometimes|string|max:20',
        ]);

        $column->update($validated);

        return response()->json(['success' => true]);
    }

    /**
     * Reorders columns on a board — expects an ordered array of column IDs.
     */
    public function reorder(Request $request, int $boardId)
    {
        $board = TaskBoard::findOrFail($boardId);
        $this->authorizeBoard($board);

        $validated = $request->validate([
            'column_ids'   => 'required|array',
            'column_ids.*' => 'integer|exists:task_columns,id',
        ]);

        foreach ($validated['column_ids'] as $position => $columnId) {
            TaskColumn::where('id', $columnId)->where('board_id', $board->id)->update(['position' => $position]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy(int $id)
    {
        $column = TaskColumn::findOrFail($id);
        $this->authorizeBoard($column->board);

        if ($column->tasks()->exists()) {
            return response()->json(['success' => false, 'message' => 'Move or delete all cards in this column first.'], 422);
        }

        $column->delete();

        return response()->json(['success' => true]);
    }
}
