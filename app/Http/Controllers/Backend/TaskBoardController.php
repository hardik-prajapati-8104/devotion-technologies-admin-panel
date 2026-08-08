<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Models\TaskBoard;
use App\Models\TaskColumn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskBoardController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            if (is_null($this->user) || ! $this->user->can('tasks.view')) {
                abort(403, 'Sorry !! You are unauthorized to view Task Management !');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $boards = TaskBoard::where(function ($q) {
                $q->where('created_by', $this->user->id)
                  ->orWhereHas('members', fn ($m) => $m->where('admin_id', $this->user->id));
            })
            ->withCount('tasks')
            ->latest()
            ->get();

        $admins = Admin::where('id', '!=', $this->user->id)->orderBy('first_name')->get();

        return view('backend.tasks.boards.index', compact('boards', 'admins'));
    }

    public function store(Request $request)
    {
        if (! $this->user->can('tasks.create')) {
            abort(403);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:20',
        ]);

        $board = TaskBoard::create(array_merge($validated, ['created_by' => $this->user->id]));

        // Sensible default columns so a new board isn't empty.
        foreach (['To Do', 'In Progress', 'Review', 'Done'] as $i => $name) {
            TaskColumn::create(['board_id' => $board->id, 'name' => $name, 'position' => $i]);
        }

        ActivityLog::record('created', 'Task Board', $board->id, "Created board \"{$board->name}\".");

        return redirect()->route('admin.tasks.boards.show', $board->id);
    }

    public function show(int $id)
    {
        $board = TaskBoard::findOrFail($id);

        if (! $board->isMember($this->user)) {
            abort(403, 'You do not have access to this board.');
        }

        $board->load([
            'columns.tasks' => fn ($q) => $q->orderBy('position'),
            'columns.tasks.assignees',
            'columns.tasks.labels',
            'columns.tasks.checklists.items',
            'labels',
            'members',
        ]);

        $boardAdmins = Admin::where('id', '!=', $this->user->id)->orderBy('first_name')->get();
        $memberIds = $board->members->pluck('id')->push($board->created_by)->all();

        return view('backend.tasks.boards.show', compact('board', 'boardAdmins', 'memberIds'));
    }

    public function addMember(Request $request, int $id)
    {
        $board = TaskBoard::findOrFail($id);
        if (! $board->isOwner($this->user)) abort(403);

        $validated = $request->validate(['admin_id' => 'required|exists:admins,id']);
        $board->members()->syncWithoutDetaching([$validated['admin_id']]);

        session()->flash('success', 'Member added to board !!');
        return back();
    }

    public function removeMember(int $id, int $adminId)
    {
        $board = TaskBoard::findOrFail($id);
        if (! $board->isOwner($this->user)) abort(403);

        $board->members()->detach($adminId);

        session()->flash('success', 'Member removed from board !!');
        return back();
    }

    public function destroy(int $id)
    {
        $board = TaskBoard::findOrFail($id);
        if (! $board->isOwner($this->user) && ! $this->user->can('tasks.delete')) abort(403);

        $board->delete();

        ActivityLog::record('deleted', 'Task Board', $id, "Deleted board \"{$board->name}\".");

        session()->flash('success', 'Board deleted !!');
        return redirect()->route('admin.tasks.boards.index');
    }
}
