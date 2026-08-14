<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\HomeBanner;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HomeBannerController extends Controller
{
    public $user;

    public function __construct(private ImageUploadService $imageUploadService)
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            if (is_null($this->user) || ! $this->user->can('home-banners.view')) {
                abort(403, 'Sorry !! You are unauthorized to view Home Banner Management !');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $banners = HomeBanner::orderBy('sort_order')->get();

        return view('backend.home-banners.index', compact('banners'));
    }

    public function create()
    {
        if (! $this->user->can('home-banners.create')) {
            abort(403);
        }

        return view('backend.home-banners.create');
    }

    public function store(Request $request)
    {
        if (! $this->user->can('home-banners.create')) {
            abort(403);
        }

        $validated = $this->validateBanner($request);
        $validated['status'] = $request->boolean('status');

        $validated['image'] = $this->imageUploadService->upload($request->file('image'), 'home-banners');
        $validated['created_by'] = $this->user->id;
        $validated['sort_order'] = HomeBanner::max('sort_order') + 1;

        $banner = HomeBanner::create($validated);

        ActivityLog::record('created', 'Home Banner', $banner->id, "Created home banner \"{$banner->title}\".");

        session()->flash('success', 'Banner created successfully !!');
        return redirect()->route('admin.home-banners.index');
    }

    public function edit(int $id)
    {
        if (! $this->user->can('home-banners.edit')) {
            abort(403);
        }

        $banner = HomeBanner::findOrFail($id);

        return view('backend.home-banners.edit', compact('banner'));
    }

    public function update(Request $request, int $id)
    {
        if (! $this->user->can('home-banners.edit')) {
            abort(403);
        }

        $banner = HomeBanner::findOrFail($id);
        $validated = $this->validateBanner($request, isUpdate: true);
        $validated['status'] = $request->boolean('status');

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $validated['image'] = $this->imageUploadService->upload($request->file('image'), 'home-banners');
        } else {
            unset($validated['image']);
        }

        $banner->update($validated);

        ActivityLog::record('updated', 'Home Banner', $banner->id, "Updated home banner \"{$banner->title}\".");

        session()->flash('success', 'Banner updated successfully !!');
        return redirect()->route('admin.home-banners.index');
    }

    public function destroy(int $id)
    {
        if (! $this->user->can('home-banners.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete this banner !');
        }

        $banner = HomeBanner::find($id);

        if (! is_null($banner)) {
            $title = $banner->title;

            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }

            $banner->delete();

            ActivityLog::record('deleted', 'Home Banner', $id, "Deleted home banner \"{$title}\".");
        }

        session()->flash('success', 'Banner deleted successfully !!');
        return back();
    }

    public function toggleStatus(int $id)
    {
        if (! $this->user->can('home-banners.edit')) {
            abort(403);
        }

        $banner = HomeBanner::findOrFail($id);
        $banner->status = ! $banner->status;
        $banner->save();

        return response()->json(['success' => true, 'status' => $banner->status]);
    }

    /**
     * Drag-and-drop reorder from the index page — expects an ordered
     * array of banner IDs.
     */
    public function reorder(Request $request)
    {
        if (! $this->user->can('home-banners.edit')) {
            abort(403);
        }

        $validated = $request->validate([
            'banner_ids'   => 'required|array',
            'banner_ids.*' => 'integer|exists:home_banners,id',
        ]);

        foreach ($validated['banner_ids'] as $position => $id) {
            HomeBanner::where('id', $id)->update(['sort_order' => $position]);
        }

        return response()->json(['success' => true]);
    }

    private function validateBanner(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'title'        => 'nullable|string|max:150',
            'subtitle'     => 'nullable|string|max:255',
            'image'        => ($isUpdate ? 'nullable' : 'required').'|image|mimes:jpg,jpeg,png,webp|max:4096',
            'button_text'  => 'nullable|string|max:50',
            'button_link'  => 'nullable|string|max:255',
            'status'       => 'nullable|boolean',
            'starts_at'    => 'nullable|date',
            'ends_at'      => 'nullable|date|after_or_equal:starts_at',
        ]);
    }
}
