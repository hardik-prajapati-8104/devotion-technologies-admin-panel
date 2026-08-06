<?php

namespace App\Http\Controllers\Backend\Security;

use App\Http\Controllers\Controller;
use App\Models\AdminLoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginAttemptController extends Controller
{
    public function index(Request $request): View
    {
        if (! Auth::guard('admin')->user()?->can('security.manage')) {
            abort(403);
        }

        $query = AdminLoginAttempt::latest('created_at');

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }

        if ($request->filled('ip')) {
            $query->where('ip', $request->input('ip'));
        }

        if ($request->filled('status')) {
            $query->where('successful', $request->input('status') === 'success');
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $attempts = $query->paginate(20)->withQueryString();

        return view('backend.security.login-attempts', compact('attempts'));
    }
}
