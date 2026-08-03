<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class ServiceCategoryController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('services.view')) {
            abort(403, 'Sorry !! You are unauthorized to view service categories !');
        }

        $categories = Cache::remember('service_categories', 10, function () {
            return ServiceCategory::withCount('services')->latest()->get();
        });

        return view('backend.service-categories.index', compact('categories'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('services.create')) {
            abort(403, 'Sorry !! You are unauthorized to create a service category !');
        }

        return view('backend.service-categories.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('services.create')) {
            abort(403, 'Sorry !! You are unauthorized to create a service category !');
        }

        $data = $request->validate([
            'name'              => 'required|string|max:150',
            'description'       => 'nullable|string',
            'status'            => 'required|boolean',
            'seo_title'         => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
        ]);

        $category = new ServiceCategory($data);
        $category->slug = ServiceCategory::generateUniqueSlug($data['name']);
        $category->save();

        Cache::forget('service_categories');
        ActivityLog::record('created', 'Service Categories', $category->id, "Created service category \"{$category->name}\".");

        session()->flash('success', $category->name.' category has been created !!');
        return redirect()->route('admin.service-categories.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('services.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit a service category !');
        }

        $category = ServiceCategory::findOrFail($id);
        return view('backend.service-categories.edit', compact('category'));
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('services.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit a service category !');
        }

        $category = ServiceCategory::findOrFail($id);

        $data = $request->validate([
            'name'              => 'required|string|max:150',
            'description'       => 'nullable|string',
            'status'            => 'required|boolean',
            'seo_title'         => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
        ]);

        if ($data['name'] !== $category->name) {
            $data['slug'] = ServiceCategory::generateUniqueSlug($data['name'], $category->id);
        }

        $category->update($data);

        Cache::forget('service_categories');
        ActivityLog::record('updated', 'Service Categories', $category->id, "Updated service category \"{$category->name}\".");

        session()->flash('success', $category->name.' category has been updated !!');
        return redirect()->route('admin.service-categories.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('services.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete a service category !');
        }

        $category = ServiceCategory::findOrFail($id);

        if ($category->services()->count() > 0) {
            session()->flash('error', 'This category still has services assigned to it and cannot be deleted.');
            return back();
        }

        $category->delete();

        Cache::forget('service_categories');
        ActivityLog::record('deleted', 'Service Categories', $id, "Deleted service category \"{$category->name}\".");

        session()->flash('success', 'Category has been deleted !!');
        return back();
    }
}
