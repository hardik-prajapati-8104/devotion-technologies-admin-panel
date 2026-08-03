<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class FaqCategoryController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('faqs.view')) {
            abort(403, 'Sorry !! You are unauthorized to view FAQ categories !');
        }

        $categories = Cache::remember('faq_categories', 10, function () {
            return FaqCategory::withCount('faqs')->ordered()->get();
        });

        return view('backend.faqs.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('faqs.create')) {
            abort(403, 'Sorry !! You are unauthorized to create an FAQ category !');
        }

        $data = $request->validate([
            'name'   => 'required|string|max:150',
            'status' => 'required|boolean',
        ]);

        $category = new FaqCategory($data);
        $category->slug = FaqCategory::generateUniqueSlug($data['name']);
        $category->save();

        Cache::forget('faq_categories');
        ActivityLog::record('created', 'FAQ Categories', $category->id, "Created FAQ category \"{$category->name}\".");

        session()->flash('success', $category->name.' category has been created !!');
        return redirect()->route('admin.faq-categories.index');
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('faqs.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit an FAQ category !');
        }

        $category = FaqCategory::findOrFail($id);

        $data = $request->validate([
            'name'   => 'required|string|max:150',
            'status' => 'required|boolean',
        ]);

        if ($data['name'] !== $category->name) {
            $data['slug'] = FaqCategory::generateUniqueSlug($data['name'], $category->id);
        }

        $category->update($data);

        Cache::forget('faq_categories');
        ActivityLog::record('updated', 'FAQ Categories', $category->id, "Updated FAQ category \"{$category->name}\".");

        session()->flash('success', $category->name.' category has been updated !!');
        return redirect()->route('admin.faq-categories.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('faqs.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete an FAQ category !');
        }

        $category = FaqCategory::findOrFail($id);

        if ($category->faqs()->count() > 0) {
            session()->flash('error', 'This category still has FAQs assigned to it and cannot be deleted.');
            return back();
        }

        $category->delete();

        Cache::forget('faq_categories');
        ActivityLog::record('deleted', 'FAQ Categories', $id, "Deleted FAQ category \"{$category->name}\".");

        session()->flash('success', 'Category has been deleted !!');
        return back();
    }
}
