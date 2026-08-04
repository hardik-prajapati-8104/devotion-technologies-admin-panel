<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('activity-logs.view')) {
            abort(403, 'Sorry !! You are unauthorized to view activity logs !');
        }

        $query = ActivityLog::with('admin')->latest();

        if ($request->filled('admin')) {
            $query->where('admin_id', $request->admin);
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->paginate(15)->withQueryString();

        // Populated from distinct existing rows so the filter dropdown
        // only ever shows modules that actually have log entries.
        $modules = ActivityLog::select('module')->distinct()->orderBy('module')->pluck('module');
        $admins = Admin::orderBy('first_name')->get();

        return view('backend.activity-logs.index', compact('logs', 'modules', 'admins'));
    }
}
