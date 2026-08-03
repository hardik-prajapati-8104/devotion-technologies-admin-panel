<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CareerApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('applications.view')) {
            abort(403, 'Sorry !! You are unauthorized to view any application !');
        }

        $query = CareerApplication::with('career')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('career')) {
            $query->where('career_id', $request->career);
        }

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('applicant_name', 'like', '%'.$request->q.'%')
                  ->orWhere('email', 'like', '%'.$request->q.'%');
            });
        }

        $applications = $query->paginate(15)->withQueryString();

        return view('backend.applications.index', compact('applications'));
    }

    public function show(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('applications.view')) {
            abort(403, 'Sorry !! You are unauthorized to view this application !');
        }

        $application = CareerApplication::with('career')->findOrFail($id);

        // Viewing an application for the first time marks it out of "new".
        if ($application->status === 'new') {
            $application->status = 'reviewing';
            $application->save();
        }

        return view('backend.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('applications.update')) {
            abort(403, 'Sorry !! You are unauthorized to update this application !');
        }

        $application = CareerApplication::findOrFail($id);

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(CareerApplication::STATUSES))],
        ]);

        $application->update($data);

        ActivityLog::record('updated', 'Applications', $application->id, "Marked application from \"{$application->applicant_name}\" as {$application->status_label}.");

        session()->flash('success', 'Application status updated !!');
        return back();
    }

    public function downloadResume(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('applications.view')) {
            abort(403);
        }

        $application = CareerApplication::findOrFail($id);

        if (! Storage::disk('public')->exists($application->resume_path)) {
            abort(404, 'Resume file not found.');
        }

        return Storage::disk('public')->download(
            $application->resume_path,
            $application->applicant_name.' - Resume.pdf'
        );
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('applications.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete this application !');
        }

        $application = CareerApplication::find($id);

        if (! is_null($application)) {
            Storage::disk('public')->delete($application->resume_path);
            $application->delete();

            ActivityLog::record('deleted', 'Applications', $id, "Deleted application from \"{$application->applicant_name}\".");
        }

        session()->flash('success', 'Application has been deleted !!');
        return back();
    }
}
