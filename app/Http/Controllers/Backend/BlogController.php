<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreBlogRequest;
use App\Http\Requests\Backend\UpdateBlogRequest;
use App\Models\ActivityLog;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('blogs.view')) {
            abort(403, 'Sorry !! You are unauthorized to view any blog !');
        }

        $query = Blog::with(['category', 'author'])->ordered();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('blog_category_id', $request->category);
        }

        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }

        $blogs = $query->paginate(15)->withQueryString();
        $categories = BlogCategory::where('status', 1)->get();

        return view('backend.blogs.index', compact('blogs', 'categories'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('blogs.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any blog !');
        }

        $categories = BlogCategory::where('status', 1)->get();
        return view('backend.blogs.create', compact('categories'));
    }

    public function store(StoreBlogRequest $request)
    {
        if (is_null($this->user) || ! $this->user->can('blogs.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any blog !');
        }

        $data = $request->validated();

        $blog = new Blog($data);
        $blog->slug = Blog::generateUniqueSlug($data['title']);
        $blog->admin_id = $this->user->id;
        $blog->is_featured = $request->boolean('is_featured');
        $blog->reading_time = $data['reading_time'] ?? Blog::estimateReadingTime($data['content'] ?? '');

        if ($request->hasFile('featured_image')) {
            $blog->featured_image = $this->imageUploadService->upload($request->file('featured_image'), 'blogs');
        }
        if ($request->hasFile('thumbnail')) {
            $blog->thumbnail = $this->imageUploadService->upload($request->file('thumbnail'), 'blogs/thumbnails');
        }
        if ($request->hasFile('og_image')) {
            $blog->og_image = $this->imageUploadService->upload($request->file('og_image'), 'blogs/seo');
        }

        $blog->save();

        if ($request->filled('tags')) {
            $blog->tags()->sync(BlogTag::resolveFromNames(explode(',', $request->tags)));
        }

        ActivityLog::record('created', 'Blogs', $blog->id, "Created blog \"{$blog->title}\".");

        session()->flash('success', $blog->title.' has been created !!');
        return redirect()->route('admin.blogs.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('blogs.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any blog !');
        }

        $blog = Blog::with('tags')->findOrFail($id);
        $categories = BlogCategory::where('status', 1)->get();

        return view('backend.blogs.edit', compact('blog', 'categories'));
    }

    public function update(UpdateBlogRequest $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('blogs.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any blog !');
        }

        $blog = Blog::findOrFail($id);
        $data = $request->validated();

        if ($data['title'] !== $blog->title) {
            $data['slug'] = Blog::generateUniqueSlug($data['title'], $blog->id);
        }

        $blog->fill($data);
        $blog->is_featured = $request->boolean('is_featured');
        $blog->reading_time = $data['reading_time'] ?? Blog::estimateReadingTime($data['content'] ?? $blog->content);

        if ($request->hasFile('featured_image')) {
            $blog->featured_image = $this->imageUploadService->replace($request->file('featured_image'), 'blogs', $blog->featured_image);
        }
        if ($request->hasFile('thumbnail')) {
            $blog->thumbnail = $this->imageUploadService->replace($request->file('thumbnail'), 'blogs/thumbnails', $blog->thumbnail);
        }
        if ($request->hasFile('og_image')) {
            $blog->og_image = $this->imageUploadService->replace($request->file('og_image'), 'blogs/seo', $blog->og_image);
        }

        $blog->save();

        $blog->tags()->sync($request->filled('tags') ? BlogTag::resolveFromNames(explode(',', $request->tags)) : []);

        ActivityLog::record('updated', 'Blogs', $blog->id, "Updated blog \"{$blog->title}\".");

        session()->flash('success', $blog->title.' has been updated !!');
        return redirect()->route('admin.blogs.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('blogs.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete any blog !');
        }

        $blog = Blog::find($id);

        if (! is_null($blog)) {
            $this->imageUploadService->delete($blog->featured_image);
            $this->imageUploadService->delete($blog->thumbnail);
            $this->imageUploadService->delete($blog->og_image);
            $blog->delete();

            ActivityLog::record('deleted', 'Blogs', $id, "Deleted blog \"{$blog->title}\".");
        }

        session()->flash('success', 'Blog has been deleted !!');
        return back();
    }

    /**
     * Read-only preview of a blog exactly as it will render — used before publishing.
     */
    public function preview(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('blogs.view')) {
            abort(403);
        }

        $blog = Blog::with(['category', 'author', 'tags'])->findOrFail($id);
        return view('backend.blogs.preview', compact('blog'));
    }

    /**
     * Clone a blog as a new draft — useful for using an existing post as a template.
     */
    public function duplicate(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('blogs.create')) {
            abort(403, 'Sorry !! You are unauthorized to duplicate any blog !');
        }

        $original = Blog::with('tags')->findOrFail($id);

        $copy = $original->replicate(['slug']);
        $copy->title = $original->title.' (Copy)';
        $copy->slug = Blog::generateUniqueSlug($copy->title);
        $copy->status = 'draft';
        $copy->publish_date = null;
        $copy->admin_id = $this->user->id;
        $copy->save();

        $copy->tags()->sync($original->tags->pluck('id'));

        ActivityLog::record('created', 'Blogs', $copy->id, "Duplicated blog \"{$original->title}\".");

        session()->flash('success', 'Blog duplicated as a new draft !!');
        return redirect()->route('admin.blogs.edit', $copy->id);
    }

    /**
     * Toggle publish / unpublish via AJAX (draft <-> published).
     */
    public function toggleStatus(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('blogs.publish')) {
            abort(403);
        }

        $blog = Blog::findOrFail($id);
        $blog->status = $blog->status === 'published' ? 'draft' : 'published';

        if ($blog->status === 'published' && ! $blog->publish_date) {
            $blog->publish_date = now();
        }

        $blog->save();

        return response()->json([
            'success' => true,
            'status'  => $blog->status,
            'message' => 'Blog status updated successfully.',
        ]);
    }
}
