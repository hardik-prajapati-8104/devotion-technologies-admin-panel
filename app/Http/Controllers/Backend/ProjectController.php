<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreProjectRequest;
use App\Http\Requests\Backend\UpdateProjectRequest;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectImage;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public $user;

    public function __construct(private ImageUploadService $imageUploadService)
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('projects.view')) {
            abort(403, 'Sorry !! You are unauthorized to view any project !');
        }

        $query = Project::with('category')->ordered();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('project_category_id', $request->category);
        }

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        }

        $projects = $query->paginate(15)->withQueryString();
        $categories = ProjectCategory::where('status', 1)->get();

        return view('backend.projects.index', compact('projects', 'categories'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('projects.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any project !');
        }

        $categories = ProjectCategory::where('status', 1)->get();
        return view('backend.projects.create', compact('categories'));
    }

    public function store(StoreProjectRequest $request)
    {
        if (is_null($this->user) || ! $this->user->can('projects.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any project !');
        }

        $data = $request->validated();

        $project = new Project($data);
        $project->slug = Project::generateUniqueSlug($data['name']);
        $project->is_featured = $request->boolean('is_featured');

        if ($request->hasFile('featured_image')) {
            $project->featured_image = $this->imageUploadService->upload($request->file('featured_image'), 'projects');
        }

        if ($request->hasFile('og_image')) {
            $project->og_image = $this->imageUploadService->upload($request->file('og_image'), 'projects/seo');
        }

        $project->save();

        $this->storeGalleryImages($request, $project);

        ActivityLog::record('created', 'Projects', $project->id, "Created project \"{$project->name}\".");

        session()->flash('success', $project->name.' has been created !!');
        return redirect()->route('admin.projects.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('projects.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any project !');
        }

        $project = Project::with('images')->findOrFail($id);
        $categories = ProjectCategory::where('status', 1)->get();

        return view('backend.projects.edit', compact('project', 'categories'));
    }

    public function update(UpdateProjectRequest $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('projects.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any project !');
        }

        $project = Project::findOrFail($id);
        $data = $request->validated();

        if ($data['name'] !== $project->name) {
            $data['slug'] = Project::generateUniqueSlug($data['name'], $project->id);
        }

        $project->fill($data);
        $project->is_featured = $request->boolean('is_featured');

        if ($request->hasFile('featured_image')) {
            $project->featured_image = $this->imageUploadService->replace($request->file('featured_image'), 'projects', $project->featured_image);
        }

        if ($request->hasFile('og_image')) {
            $project->og_image = $this->imageUploadService->replace($request->file('og_image'), 'projects/seo', $project->og_image);
        }

        $project->save();

        $this->storeGalleryImages($request, $project);

        ActivityLog::record('updated', 'Projects', $project->id, "Updated project \"{$project->name}\".");

        session()->flash('success', $project->name.' has been updated !!');
        return redirect()->route('admin.projects.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('projects.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete any project !');
        }

        $project = Project::with('images')->find($id);

        if (! is_null($project)) {
            $this->imageUploadService->delete($project->featured_image);
            $this->imageUploadService->delete($project->og_image);

            foreach ($project->images as $image) {
                $this->imageUploadService->delete($image->image_path);
            }

            $project->delete(); // project_images cascade-delete at the DB level

            ActivityLog::record('deleted', 'Projects', $id, "Deleted project \"{$project->name}\".");
        }

        session()->flash('success', 'Project has been deleted !!');
        return back();
    }

    /**
     * Remove a single gallery image via AJAX (used by the gallery manager on the edit page).
     */
    public function destroyGalleryImage(int $projectId, int $imageId)
    {
        if (is_null($this->user) || ! $this->user->can('projects.edit')) {
            abort(403);
        }

        $image = ProjectImage::where('project_id', $projectId)->findOrFail($imageId);
        $this->imageUploadService->delete($image->image_path);
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Gallery image removed.']);
    }

    public function toggleFeatured(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('projects.edit')) {
            abort(403);
        }

        $project = Project::findOrFail($id);
        $project->is_featured = ! $project->is_featured;
        $project->save();

        return response()->json(['success' => true, 'is_featured' => $project->is_featured]);
    }

    private function storeGalleryImages(Request $request, Project $project): void
    {
        if (! $request->hasFile('gallery')) {
            return;
        }

        $startOrder = $project->images()->max('display_order') ?? 0;

        foreach ($request->file('gallery') as $i => $file) {
            $path = $this->imageUploadService->upload($file, 'projects/gallery');

            ProjectImage::create([
                'project_id'    => $project->id,
                'image_path'    => $path,
                'display_order' => $startOrder + $i + 1,
            ]);
        }
    }
}
