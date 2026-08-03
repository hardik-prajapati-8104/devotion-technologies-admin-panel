<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ContactEnquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class EnquiryController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('enquiries.view')) {
            abort(403, 'Sorry !! You are unauthorized to view any enquiry !');
        }

        $query = ContactEnquiry::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->q.'%')
                  ->orWhere('email', 'like', '%'.$request->q.'%')
                  ->orWhere('subject', 'like', '%'.$request->q.'%');
            });
        }

        $enquiries = $query->paginate(15)->withQueryString();

        return view('backend.enquiries.index', compact('enquiries'));
    }

    public function show(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('enquiries.view')) {
            abort(403, 'Sorry !! You are unauthorized to view this enquiry !');
        }

        $enquiry = ContactEnquiry::findOrFail($id);

        if ($enquiry->status === 'new') {
            $enquiry->status = 'read';
            $enquiry->save();
        }

        return view('backend.enquiries.show', compact('enquiry'));
    }

    public function updateStatus(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('enquiries.update')) {
            abort(403, 'Sorry !! You are unauthorized to update this enquiry !');
        }

        $enquiry = ContactEnquiry::findOrFail($id);

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(ContactEnquiry::STATUSES))],
        ]);

        $enquiry->update($data);

        ActivityLog::record('updated', 'Enquiries', $enquiry->id, "Marked enquiry from \"{$enquiry->name}\" as {$enquiry->status_label}.");

        session()->flash('success', 'Enquiry status updated !!');
        return back();
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('enquiries.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete this enquiry !');
        }

        $enquiry = ContactEnquiry::find($id);

        if (! is_null($enquiry)) {
            $enquiry->delete();
            ActivityLog::record('deleted', 'Enquiries', $id, "Deleted enquiry from \"{$enquiry->name}\".");
        }

        session()->flash('success', 'Enquiry has been deleted !!');
        return back();
    }

    public function bulkDestroy(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('enquiries.delete')) {
            abort(403);
        }

        $ids = (array) $request->input('ids', []);
        ContactEnquiry::whereIn('id', $ids)->delete();

        session()->flash('success', count($ids).' enquiry(ies) have been deleted !!');
        return back();
    }
}
