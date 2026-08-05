<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class NoticeController extends Controller
{
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
        if (is_null($this->user) || ! $this->user->can('notices.view')) {
            abort(403, 'Sorry !! You are unauthorized to view notices !');
        }

        $query = Notice::with('author')->latest();

        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $notices = $query->paginate(20)->withQueryString();

        return view('backend.notices.index', compact('notices'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('notices.create')) {
            abort(403, 'Sorry !! You are unauthorized to create a notice !');
        }

        $roles = Role::where('guard_name', 'admin')->pluck('name');

        return view('backend.notices.create', compact('roles'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('notices.create')) {
            abort(403, 'Sorry !! You are unauthorized to create a notice !');
        }

        $validated = $this->validateNotice($request);
        $validated['created_by'] = $this->user->id;

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $notice = Notice::create($validated);

        ActivityLog::record('created', 'Notice', $notice->id, "Created notice \"{$notice->title}\".");

        session()->flash('success', 'Notice has been created successfully !!');
        return redirect()->route('admin.notices.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('notices.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit this notice !');
        }

        $notice = Notice::findOrFail($id);
        $roles = Role::where('guard_name', 'admin')->pluck('name');

        return view('backend.notices.edit', compact('notice', 'roles'));
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('notices.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit this notice !');
        }

        $notice = Notice::findOrFail($id);
        $validated = $this->validateNotice($request);

        if ($validated['status'] === 'published' && empty($notice->published_at) && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $notice->update($validated);

        ActivityLog::record('updated', 'Notice', $notice->id, "Updated notice \"{$notice->title}\".");

        session()->flash('success', 'Notice has been updated successfully !!');
        return redirect()->route('admin.notices.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('notices.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete this notice !');
        }

        $notice = Notice::find($id);

        if (! is_null($notice)) {
            $title = $notice->title;
            $notice->delete();

            ActivityLog::record('deleted', 'Notice', $id, "Deleted notice \"{$title}\".");
        }

        session()->flash('success', 'Notice has been deleted successfully !!');
        return back();
    }

    private function validateNotice(Request $request): array
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:150',
            'body'          => 'required|string',
            'status'        => 'required|in:draft,published',
            'target_roles'  => 'nullable|array',
            'target_roles.*' => 'string',
            'published_at'  => 'nullable|date',
            'expires_at'    => 'nullable|date|after_or_equal:published_at',
        ]);

        // Empty array means "everyone" — store as null rather than [].
        $validated['target_roles'] = empty($validated['target_roles']) ? null : $validated['target_roles'];

        return $validated;
    }
}
