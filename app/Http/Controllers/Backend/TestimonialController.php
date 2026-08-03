<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreTestimonialRequest;
use App\Http\Requests\Backend\UpdateTestimonialRequest;
use App\Models\ActivityLog;
use App\Models\Testimonial;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestimonialController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('testimonials.view')) {
            abort(403, 'Sorry !! You are unauthorized to view any testimonial !');
        }

        $query = Testimonial::ordered();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->where('client_name', 'like', '%'.$request->q.'%');
        }

        $testimonials = $query->paginate(15)->withQueryString();

        return view('backend.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('testimonials.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any testimonial !');
        }

        return view('backend.testimonials.create');
    }

    public function store(StoreTestimonialRequest $request)
    {
        if (is_null($this->user) || ! $this->user->can('testimonials.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any testimonial !');
        }

        $data = $request->validated();

        $testimonial = new Testimonial($data);
        $testimonial->is_featured = $request->boolean('is_featured');

        if ($request->hasFile('profile_image')) {
            $testimonial->profile_image = $this->imageUploadService->upload($request->file('profile_image'), 'testimonials');
        }

        $testimonial->save();

        ActivityLog::record('created', 'Testimonials', $testimonial->id, "Created testimonial from \"{$testimonial->client_name}\".");

        session()->flash('success', 'Testimonial has been created !!');
        return redirect()->route('admin.testimonials.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('testimonials.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any testimonial !');
        }

        $testimonial = Testimonial::findOrFail($id);
        return view('backend.testimonials.edit', compact('testimonial'));
    }

    public function update(UpdateTestimonialRequest $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('testimonials.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any testimonial !');
        }

        $testimonial = Testimonial::findOrFail($id);
        $data = $request->validated();

        $testimonial->fill($data);
        $testimonial->is_featured = $request->boolean('is_featured');

        if ($request->hasFile('profile_image')) {
            $testimonial->profile_image = $this->imageUploadService->replace($request->file('profile_image'), 'testimonials', $testimonial->profile_image);
        }

        $testimonial->save();

        ActivityLog::record('updated', 'Testimonials', $testimonial->id, "Updated testimonial from \"{$testimonial->client_name}\".");

        session()->flash('success', 'Testimonial has been updated !!');
        return redirect()->route('admin.testimonials.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('testimonials.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete any testimonial !');
        }

        $testimonial = Testimonial::find($id);

        if (! is_null($testimonial)) {
            $this->imageUploadService->delete($testimonial->profile_image);
            $testimonial->delete();

            ActivityLog::record('deleted', 'Testimonials', $id, "Deleted testimonial from \"{$testimonial->client_name}\".");
        }

        session()->flash('success', 'Testimonial has been deleted !!');
        return back();
    }

    /**
     * Persist new display_order values after a drag-and-drop reorder (AJAX).
     */
    public function reorder(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('testimonials.edit')) {
            abort(403);
        }

        foreach ((array) $request->input('order', []) as $position => $id) {
            Testimonial::where('id', $id)->update(['display_order' => $position]);
        }

        return response()->json(['success' => true]);
    }
}
