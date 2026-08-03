<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProjectCategoryController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('projects.view')) {
            abort(403, 'Sorry !! You are unauthorized to view project categories !');
        }

        $categories = Cache::remember('project_categories', 10, function () {
            return ProjectCategory::withCount('projects')->latest()->get();
        });

        return view('backend.project-categories.index', compact('categories'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('projects.create')) {
            abort(403, 'Sorry !! You are unauthorized to create a project category !');
        }

        return view('backend.project-categories.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('projects.create')) {
            abort(403, 'Sorry !! You are unauthorized to create a project category !');
        }

        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ]);

        $category = new ProjectCategory($data);
        $category->slug = ProjectCategory::generateUniqueSlug($data['name']);
        $category->save();

        Cache::forget('project_categories');
        ActivityLog::record('created', 'Project Categories', $category->id, "Created project category \"{$category->name}\".");

        session()->flash('success', $category->name.' category has been created !!');
        return redirect()->route('admin.project-categories.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('projects.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit a project category !');
        }

        $category = ProjectCategory::findOrFail($id);
        return view('backend.project-categories.edit', compact('category'));
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('projects.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit a project category !');
        }

        $category = ProjectCategory::findOrFail($id);

        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ]);

        if ($data['name'] !== $category->name) {
            $data['slug'] = ProjectCategory::generateUniqueSlug($data['name'], $category->id);
        }

        $category->update($data);

        Cache::forget('project_categories');
        ActivityLog::record('updated', 'Project Categories', $category->id, "Updated project category \"{$category->name}\".");

        session()->flash('success', $category->name.' category has been updated !!');
        return redirect()->route('admin.project-categories.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('projects.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete a project category !');
        }

        $category = ProjectCategory::findOrFail($id);

        if ($category->projects()->count() > 0) {
            session()->flash('error', 'This category still has projects assigned to it and cannot be deleted.');
            return back();
        }

        $category->delete();

        Cache::forget('project_categories');
        ActivityLog::record('deleted', 'Project Categories', $id, "Deleted project category \"{$category->name}\".");

        session()->flash('success', 'Category has been deleted !!');
        return back();
    }
}
