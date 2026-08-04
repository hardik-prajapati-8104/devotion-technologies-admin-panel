<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Media;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('media.view')) {
            abort(403, 'Sorry !! You are unauthorized to view the media library !');
        }

        $query = Media::with('uploadedBy')->latest();

        if ($request->filled('type')) {
            $query->where('mime_type', 'like', $request->type.'/%');
        }

        if ($request->filled('q')) {
            $query->where('original_name', 'like', '%'.$request->q.'%');
        }

        $media = $query->paginate(24)->withQueryString();

        return view('backend.media.index', compact('media'));
    }

    /**
     * Handle one or more files uploaded at once from the library's
     * upload widget. Only images are accepted here — the library is
     * an image asset manager, matching spec §17.
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('media.upload')) {
            abort(403, 'Sorry !! You are unauthorized to upload media !');
        }

        $request->validate([
            'files'   => 'required|array|min:1',
            'files.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $uploaded = [];

        foreach ($request->file('files') as $file) {
            $path = $this->imageUploadService->upload($file, 'media');

            $uploaded[] = Media::create([
                'admin_id'      => $this->user->id,
                'original_name' => $file->getClientOriginalName(),
                'file_path'     => $path,
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
                'folder'        => 'media',
            ]);
        }

        ActivityLog::record('created', 'Media', null, count($uploaded).' file(s) uploaded to the media library.');

        session()->flash('success', count($uploaded).' file(s) uploaded successfully !!');
        return back();
    }

    public function show(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('media.view')) {
            abort(403, 'Sorry !! You are unauthorized to view the media library !');
        }

        $item = Media::with('uploadedBy')->findOrFail($id);

        return view('backend.media.show', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('media.upload')) {
            abort(403);
        }

        $media = Media::findOrFail($id);
        $media->alt_text = $request->input('alt_text');
        $media->save();

        return response()->json(['success' => true]);
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('media.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete media !');
        }

        $media = Media::find($id);

        if (! is_null($media)) {
            Storage::disk('public')->delete($media->file_path);
            $media->delete();

            ActivityLog::record('deleted', 'Media', $id, "Deleted media file \"{$media->original_name}\".");
        }

        session()->flash('success', 'File has been deleted !!');
        return back();
    }
}
