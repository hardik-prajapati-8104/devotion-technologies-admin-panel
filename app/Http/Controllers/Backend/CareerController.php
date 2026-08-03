<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreCareerRequest;
use App\Http\Requests\Backend\UpdateCareerRequest;
use App\Models\ActivityLog;
use App\Models\Career;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CareerController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('careers.view')) {
            abort(403, 'Sorry !! You are unauthorized to view any career listing !');
        }

        $query = Career::withCount('applications')->ordered();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->where('title', 'like', '%'.$request->q.'%');
        }

        $careers = $query->paginate(15)->withQueryString();

        return view('backend.careers.index', compact('careers'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('careers.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any career listing !');
        }

        return view('backend.careers.create');
    }

    public function store(StoreCareerRequest $request)
    {
        if (is_null($this->user) || ! $this->user->can('careers.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any career listing !');
        }

        $data = $request->validated();

        $career = new Career($data);
        $career->slug = Career::generateUniqueSlug($data['title']);
        $career->is_featured = $request->boolean('is_featured');
        $career->save();

        ActivityLog::record('created', 'Careers', $career->id, "Created job listing \"{$career->title}\".");

        session()->flash('success', $career->title.' has been created !!');
        return redirect()->route('admin.careers.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('careers.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any career listing !');
        }

        $career = Career::findOrFail($id);
        return view('backend.careers.edit', compact('career'));
    }

    public function update(UpdateCareerRequest $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('careers.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any career listing !');
        }

        $career = Career::findOrFail($id);
        $data = $request->validated();

        if ($data['title'] !== $career->title) {
            $data['slug'] = Career::generateUniqueSlug($data['title'], $career->id);
        }

        $career->fill($data);
        $career->is_featured = $request->boolean('is_featured');
        $career->save();

        ActivityLog::record('updated', 'Careers', $career->id, "Updated job listing \"{$career->title}\".");

        session()->flash('success', $career->title.' has been updated !!');
        return redirect()->route('admin.careers.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('careers.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete any career listing !');
        }

        $career = Career::find($id);

        if (! is_null($career)) {
            $career->delete(); // applications are kept (career_id becomes null via nullOnDelete)

            ActivityLog::record('deleted', 'Careers', $id, "Deleted job listing \"{$career->title}\".");
        }

        session()->flash('success', 'Job listing has been deleted !!');
        return back();
    }
}
