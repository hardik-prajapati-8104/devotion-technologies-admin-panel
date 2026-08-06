<?php

namespace App\Http\Controllers\Backend\Security;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AdminIpRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IpRuleController extends Controller
{
    public function index(): View
    {
        $this->authorizeSecurity();

        return view('backend.security.ip-rules', [
            'whitelist' => AdminIpRule::whitelist()->latest()->get(),
            'blacklist' => AdminIpRule::blacklist()->latest()->get(),
            'currentIp' => request()->ip(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeSecurity();

        $request->validate([
            'ip'   => ['required', 'string', 'max:64', 'regex:/^[0-9a-fA-F:.\/]+$/'],
            'type' => 'required|in:whitelist,blacklist',
            'note' => 'nullable|string|max:255',
        ]);

        // Guard rail: don't let an admin lock themselves out by
        // blacklisting, or by whitelisting everyone EXCEPT, their own
        // current IP.
        if ($request->input('type') === 'blacklist' && $request->input('ip') === $request->ip()) {
            return back()->withErrors(['ip' => 'You cannot blacklist the IP address you are currently connected from.']);
        }

        AdminIpRule::create([
            'ip'         => $request->input('ip'),
            'type'       => $request->input('type'),
            'note'       => $request->input('note'),
            'created_by' => Auth::guard('admin')->id(),
        ]);

        ActivityLog::record('ip-rule-added', 'Security', Auth::guard('admin')->id(), "Added {$request->input('type')} rule for {$request->input('ip')}.");

        return back()->with('success', 'IP rule added.');
    }

    public function destroy(AdminIpRule $ipRule): RedirectResponse
    {
        $this->authorizeSecurity();

        $ipRule->delete();

        ActivityLog::record('ip-rule-removed', 'Security', Auth::guard('admin')->id(), "Removed {$ipRule->type} rule for {$ipRule->ip}.");

        return back()->with('success', 'IP rule removed.');
    }

    private function authorizeSecurity(): void
    {
        if (!Auth::guard('admin')->user()->can('security.manage')) {
            abort(403);
        }
    }
}
