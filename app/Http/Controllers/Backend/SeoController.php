<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SeoSetting;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeoController extends Controller
{
    public $user;

    public function __construct(private ImageUploadService $imageUploadService)
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    /**
     * List every page's SEO record, seeding any that don't exist yet
     * from SeoSetting::DEFAULT_PAGES so the list is always complete.
     */
    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('seo.view')) {
            abort(403, 'Sorry !! You are unauthorized to view SEO settings !');
        }

        foreach (SeoSetting::DEFAULT_PAGES as $key => $label) {
            SeoSetting::firstOrCreate(['page_key' => $key], ['page_label' => $label]);
        }

        $pages = SeoSetting::orderBy('page_label')->get();

        return view('backend.seo.index', compact('pages'));
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('seo.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit SEO settings !');
        }

        $page = SeoSetting::findOrFail($id);
        return view('backend.seo.edit', compact('page'));
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('seo.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit SEO settings !');
        }

        $page = SeoSetting::findOrFail($id);

        $data = $request->validate([
            'seo_title'            => 'nullable|string|max:255',
            'meta_description'     => 'nullable|string|max:500',
            'focus_keyword'        => 'nullable|string|max:150',
            'canonical_url'        => 'nullable|url|max:255',
            'robots_meta'          => 'nullable|string|max:100',
            'og_title'             => 'nullable|string|max:255',
            'og_description'       => 'nullable|string|max:500',
            'og_image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'twitter_title'        => 'nullable|string|max:255',
            'twitter_description'  => 'nullable|string|max:500',
            'twitter_image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $page->fill($data);

        if ($request->hasFile('og_image')) {
            $page->og_image = $this->imageUploadService->replace($request->file('og_image'), 'seo', $page->og_image);
        }

        if ($request->hasFile('twitter_image')) {
            $page->twitter_image = $this->imageUploadService->replace($request->file('twitter_image'), 'seo', $page->twitter_image);
        }

        $page->save();

        ActivityLog::record('updated', 'SEO', $page->id, "Updated SEO settings for \"{$page->page_label}\".");

        session()->flash('success', 'SEO settings for '.$page->page_label.' have been updated !!');
        return redirect()->route('admin.seo.index');
    }
}
