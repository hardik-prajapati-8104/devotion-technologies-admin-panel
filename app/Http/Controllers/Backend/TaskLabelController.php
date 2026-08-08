<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\TaskBoard;
use App\Models\TaskLabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskLabelController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function store(Request $request, int $boardId)
    {
        $board = TaskBoard::findOrFail($boardId);
        if (! $board->isMember($this->user)) abort(403);

        $validated = $request->validate([
            'name'  => 'required|string|max:50',
            'color' => 'required|string|max:20',
        ]);

        $label = TaskLabel::create(array_merge($validated, ['board_id' => $board->id]));

        return response()->json(['success' => true, 'label' => $label]);
    }

    public function destroy(int $id)
    {
        $label = TaskLabel::findOrFail($id);
        if (! $label->board->isMember($this->user)) abort(403);

        $label->delete();

        return response()->json(['success' => true]);
    }
}
