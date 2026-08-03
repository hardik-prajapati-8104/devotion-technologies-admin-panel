<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BlogCategoryController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('blogs.view')) {
            abort(403, 'Sorry !! You are unauthorized to view blog categories !');
        }

        $categories = Cache::remember('blog_categories', 10, function () {
            return BlogCategory::withCount('blogs')->latest()->get();
        });

        return view('backend.blog-categories.index', compact('categories'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('blogs.create')) {
            abort(403, 'Sorry !! You are unauthorized to create a blog category !');
        }

        return view('backend.blog-categories.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('blogs.create')) {
            abort(403, 'Sorry !! You are unauthorized to create a blog category !');
        }

        $data = $request->validate([
            'name'              => 'required|string|max:150',
            'description'       => 'nullable|string',
            'status'            => 'required|boolean',
            'seo_title'         => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
        ]);

        $category = new BlogCategory($data);
        $category->slug = BlogCategory::generateUniqueSlug($data['name']);
        $category->save();

        Cache::forget('blog_categories');
        ActivityLog::record('created', 'Blog Categories', $category->id, "Created blog category \"{$category->name}\".");

        session()->flash('success', $category->name.' category has been created !!');
        return redirect()->route('admin.blog-categories.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('blogs.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit a blog category !');
        }

        $category = BlogCategory::findOrFail($id);
        return view('backend.blog-categories.edit', compact('category'));
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('blogs.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit a blog category !');
        }

        $category = BlogCategory::findOrFail($id);

        $data = $request->validate([
            'name'              => 'required|string|max:150',
            'description'       => 'nullable|string',
            'status'            => 'required|boolean',
            'seo_title'         => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
        ]);

        if ($data['name'] !== $category->name) {
            $data['slug'] = BlogCategory::generateUniqueSlug($data['name'], $category->id);
        }

        $category->update($data);

        Cache::forget('blog_categories');
        ActivityLog::record('updated', 'Blog Categories', $category->id, "Updated blog category \"{$category->name}\".");

        session()->flash('success', $category->name.' category has been updated !!');
        return redirect()->route('admin.blog-categories.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('blogs.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete a blog category !');
        }

        $category = BlogCategory::findOrFail($id);

        if ($category->blogs()->count() > 0) {
            session()->flash('error', 'This category still has blogs assigned to it and cannot be deleted.');
            return back();
        }

        $category->delete();

        Cache::forget('blog_categories');
        ActivityLog::record('deleted', 'Blog Categories', $id, "Deleted blog category \"{$category->name}\".");

        session()->flash('success', 'Category has been deleted !!');
        return back();
    }
}
