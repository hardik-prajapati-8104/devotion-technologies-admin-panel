<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreFaqRequest;
use App\Http\Requests\Backend\UpdateFaqRequest;
use App\Models\ActivityLog;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaqController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('faqs.view')) {
            abort(403, 'Sorry !! You are unauthorized to view any FAQ !');
        }

        $query = Faq::with('category')->ordered();

        if ($request->filled('category')) {
            $query->where('faq_category_id', $request->category);
        }

        if ($request->filled('q')) {
            $query->where('question', 'like', '%'.$request->q.'%');
        }

        $faqs = $query->paginate(20)->withQueryString();
        $categories = FaqCategory::where('status', 1)->ordered()->get();

        return view('backend.faqs.index', compact('faqs', 'categories'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('faqs.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any FAQ !');
        }

        $categories = FaqCategory::where('status', 1)->ordered()->get();
        return view('backend.faqs.create', compact('categories'));
    }

    public function store(StoreFaqRequest $request)
    {
        if (is_null($this->user) || ! $this->user->can('faqs.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any FAQ !');
        }

        $faq = Faq::create($request->validated());

        ActivityLog::record('created', 'FAQs', $faq->id, "Created FAQ \"{$faq->question}\".");

        session()->flash('success', 'FAQ has been created !!');
        return redirect()->route('admin.faqs.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('faqs.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any FAQ !');
        }

        $faq = Faq::findOrFail($id);
        $categories = FaqCategory::where('status', 1)->ordered()->get();

        return view('backend.faqs.edit', compact('faq', 'categories'));
    }

    public function update(UpdateFaqRequest $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('faqs.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any FAQ !');
        }

        $faq = Faq::findOrFail($id);
        $faq->update($request->validated());

        ActivityLog::record('updated', 'FAQs', $faq->id, "Updated FAQ \"{$faq->question}\".");

        session()->flash('success', 'FAQ has been updated !!');
        return redirect()->route('admin.faqs.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('faqs.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete any FAQ !');
        }

        $faq = Faq::find($id);

        if (! is_null($faq)) {
            $faq->delete();
            ActivityLog::record('deleted', 'FAQs', $id, "Deleted FAQ \"{$faq->question}\".");
        }

        session()->flash('success', 'FAQ has been deleted !!');
        return back();
    }

    public function toggleStatus(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('faqs.edit')) {
            abort(403);
        }

        $faq = Faq::findOrFail($id);
        $faq->status = ! $faq->status;
        $faq->save();

        return response()->json(['success' => true, 'status' => $faq->status]);
    }

    /**
     * Persist new display_order values after a drag-and-drop reorder (AJAX).
     */
    public function reorder(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('faqs.edit')) {
            abort(403);
        }

        foreach ((array) $request->input('order', []) as $position => $id) {
            Faq::where('id', $id)->update(['display_order' => $position]);
        }

        return response()->json(['success' => true]);
    }
}
