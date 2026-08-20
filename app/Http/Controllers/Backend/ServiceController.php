<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreServiceRequest;
use App\Http\Requests\Backend\UpdateServiceRequest;
use App\Models\ActivityLog;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ServiceController extends Controller
{
    public $user;
    protected ImageUploadService $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;

        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user = Auth::guard('admin')->user();

        if (is_null($user) || ! $user->can('services.view')) {
            abort(403, 'Sorry !! You are unauthorized to view any service !');
        }

        $query = Service::with('category')->ordered();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('service_category_id', $request->category);
        }

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        }

        $services = $query->paginate(15)->withQueryString();
        $categories = ServiceCategory::where('status', 1)->get();

        return view('backend.services.index', compact('services', 'categories'));
    }

    public function create()
    {
        $user = Auth::guard('admin')->user();

        if (is_null($user) || ! $user->can('services.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any service !');
        }

        $categories = ServiceCategory::where('status', 1)->get();
        return view('backend.services.create', compact('categories'));
    }

    public function store(StoreServiceRequest $request)
    {
        $user = Auth::guard('admin')->user();

        if (is_null($user) || ! $user->can('services.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any service !');
        }

        $data = $request->validated();

        $service = new Service($data);
        $service->slug = Service::generateUniqueSlug($data['name']);
        $service->is_featured = $request->boolean('is_featured');

        if ($request->hasFile('featured_image')) {
            $service->featured_image = $this->imageUploadService->upload($request->file('featured_image'), 'services');
        }

        if ($request->hasFile('og_image')) {
            $service->og_image = $this->imageUploadService->upload($request->file('og_image'), 'services/seo');
        }

        if ($request->hasFile('brochure')) {
            $service->brochure = $request->file('brochure')->store('services/brochures', 'public');
        }

        $service->save();

        Cache::forget('services_count');
        ActivityLog::record('created', 'Services', $service->id, "Created service \"{$service->name}\".");

        session()->flash('success', $service->name.' has been created !!');
        return redirect()->route('admin.services.index');
    }

    public function edit(int $id)
    {
        $user = Auth::guard('admin')->user();

        if (is_null($user) || ! $user->can('services.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any service !');
        }

        $service = Service::findOrFail($id);
        $categories = ServiceCategory::where('status', 1)->get();

        return view('backend.services.edit', compact('service', 'categories'));
    }

    public function update(UpdateServiceRequest $request, int $id)
    {
        $user = Auth::guard('admin')->user();

        if (is_null($user) || ! $user->can('services.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any service !');
        }

        $service = Service::findOrFail($id);
        $data = $request->validated();

        if ($data['name'] !== $service->name) {
            $data['slug'] = Service::generateUniqueSlug($data['name'], $service->id);
        }

        $service->fill($data);
        $service->is_featured = $request->boolean('is_featured');

        if ($request->hasFile('featured_image')) {
            $service->featured_image = $this->imageUploadService->replace($request->file('featured_image'), 'services', $service->featured_image);
        }

        if ($request->hasFile('og_image')) {
            $service->og_image = $this->imageUploadService->replace($request->file('og_image'), 'services/seo', $service->og_image);
        }

        if ($request->hasFile('brochure')) {
            if ($service->brochure) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($service->brochure);
            }
            $service->brochure = $request->file('brochure')->store('services/brochures', 'public');
        } elseif ($request->boolean('remove_brochure') && $service->brochure) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($service->brochure);
            $service->brochure = null;
        }

        $service->save();

        ActivityLog::record('updated', 'Services', $service->id, "Updated service \"{$service->name}\".");

        session()->flash('success', $service->name.' has been updated !!');
        return redirect()->route('admin.services.index');
    }

    public function destroy(int $id)
    {
        $user = Auth::guard('admin')->user();

        if (is_null($user) || ! $user->can('services.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete any service !');
        }

        $service = Service::find($id);

        if (! is_null($service)) {
            $this->imageUploadService->delete($service->featured_image);
            $this->imageUploadService->delete($service->og_image);
            if ($service->brochure) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($service->brochure);
            }
            $service->delete();

            ActivityLog::record('deleted', 'Services', $id, "Deleted service \"{$service->name}\".");
        }

        session()->flash('success', 'Service has been deleted !!');
        return back();
    }

    /**
     * Bulk delete selected services (checkbox list on the index table).
     */
    public function bulkDestroy(Request $request)
    {
        $user = Auth::guard('admin')->user();

        if (is_null($user) || ! $user->can('services.delete')) {
            abort(403);
        }

        $ids = (array) $request->input('ids', []);
        Service::whereIn('id', $ids)->get()->each(function (Service $service) {
            $this->imageUploadService->delete($service->featured_image);
            $service->delete();
        });

        session()->flash('success', count($ids).' service(s) have been deleted !!');
        return back();
    }

    /**
     * Toggle publish / unpublish via AJAX.
     */
    public function toggleStatus(int $id)
    {
        $user = Auth::guard('admin')->user();

        if (is_null($user) || ! $user->can('services.edit')) {
            abort(403);
        }

        $service = Service::findOrFail($id);
        $service->status = ! $service->status;
        $service->save();

        return response()->json([
            'success' => true,
            'status'  => $service->status,
            'message' => 'Service status updated successfully.',
        ]);
    }
}
