<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AdminSecuritySetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordChangeController extends Controller
{
    public function show(): View
    {
        $settings = AdminSecuritySetting::current();

        return view('backend.auth.force-password-change', [
            'policyDescription' => $settings->passwordPolicyDescription(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = AdminSecuritySetting::current();
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'current_password' => ['required', 'current_password:admin'],
            'password' => $settings->passwordRules(),
        ]);

        $admin->password = Hash::make($request->input('password'));
        $admin->must_change_password = false;
        $admin->password_changed_at = now();
        $admin->save();

        ActivityLog::record('password-changed', 'Auth', $admin->id, 'Admin changed their password.');

        return redirect()->route('admin.dashboard')->with('success', 'Password updated.');
    }
}
