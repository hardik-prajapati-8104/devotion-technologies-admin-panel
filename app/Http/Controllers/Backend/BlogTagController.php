<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BlogTag;
use Illuminate\Support\Facades\Auth;

class BlogTagController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    /**
     * Tags are created automatically from the comma-separated field on
     * the blog form (see BlogTag::resolveFromNames). This page exists
     * for visibility and cleanup of unused tags.
     */
    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('blogs.view')) {
            abort(403, 'Sorry !! You are unauthorized to view blog tags !');
        }

        $tags = BlogTag::withCount('blogs')->orderByDesc('blogs_count')->get();

        return view('backend.blog-tags.index', compact('tags'));
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('blogs.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete a blog tag !');
        }

        $tag = BlogTag::findOrFail($id);
        $tag->delete(); // pivot rows cascade-delete at the DB level

        ActivityLog::record('deleted', 'Blog Tags', $id, "Deleted blog tag \"{$tag->name}\".");

        session()->flash('success', 'Tag has been deleted !!');
        return back();
    }
}
