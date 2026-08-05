<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('announcements.view')) {
            abort(403, 'Sorry !! You are unauthorized to view announcements !');
        }

        $query = Announcement::with('author')->latest('is_pinned')->latest();

        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $announcements = $query->paginate(20)->withQueryString();

        return view('backend.announcements.index', compact('announcements'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('announcements.create')) {
            abort(403, 'Sorry !! You are unauthorized to create an announcement !');
        }

        return view('backend.announcements.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('announcements.create')) {
            abort(403, 'Sorry !! You are unauthorized to create an announcement !');
        }

        $validated = $this->validateAnnouncement($request);
        $validated['created_by'] = $this->user->id;

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $announcement = Announcement::create($validated);

        ActivityLog::record('created', 'Announcement', $announcement->id, "Created announcement \"{$announcement->title}\".");

        session()->flash('success', 'Announcement has been created successfully !!');
        return redirect()->route('admin.announcements.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('announcements.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit this announcement !');
        }

        $announcement = Announcement::findOrFail($id);

        return view('backend.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('announcements.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit this announcement !');
        }

        $announcement = Announcement::findOrFail($id);
        $validated = $this->validateAnnouncement($request);

        if ($validated['status'] === 'published' && empty($announcement->published_at) && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $announcement->update($validated);

        ActivityLog::record('updated', 'Announcement', $announcement->id, "Updated announcement \"{$announcement->title}\".");

        session()->flash('success', 'Announcement has been updated successfully !!');
        return redirect()->route('admin.announcements.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('announcements.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete this announcement !');
        }

        $announcement = Announcement::find($id);

        if (! is_null($announcement)) {
            $title = $announcement->title;
            $announcement->delete();

            ActivityLog::record('deleted', 'Announcement', $id, "Deleted announcement \"{$title}\".");
        }

        session()->flash('success', 'Announcement has been deleted successfully !!');
        return back();
    }

    private function validateAnnouncement(Request $request): array
    {
        return $request->validate([
            'title'        => 'required|string|max:150',
            'body'         => 'required|string',
            'status'       => 'required|in:draft,published',
            'is_pinned'    => 'nullable|boolean',
            'published_at' => 'nullable|date',
            'expires_at'   => 'nullable|date|after_or_equal:published_at',
        ]);
    }
}
