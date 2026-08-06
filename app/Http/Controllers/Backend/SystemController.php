<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class SystemController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    /**
     * Clears config/route/view/application caches from the admin UI.
     * Gated to superadmin (or 'settings.edit' as a fallback for other
     * roles you trust with this) — running artisan commands from a web
     * request is powerful, so keep this locked down.
     */
    public function clearCache(Request $request)
    {
        if (is_null($this->user) || (! $this->user->hasRole('superadmin') && ! $this->user->can('settings.edit'))) {
            abort(403, 'Sorry !! You are unauthorized to clear the cache !');
        }

        $results = [];

        foreach (['config:clear', 'route:clear', 'view:clear', 'cache:clear'] as $command) {
            try {
                Artisan::call($command);
                $results[$command] = 'ok';
            } catch (\Throwable $e) {
                $results[$command] = 'failed: '.$e->getMessage();
            }
        }

        ActivityLog::record('cleared', 'System Cache', null, 'Cleared application cache from the admin panel.');

        $failed = array_filter($results, fn ($r) => $r !== 'ok');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => empty($failed),
                'results' => $results,
            ]);
        }

        session()->flash(empty($failed) ? 'success' : 'error', empty($failed)
            ? 'Cache cleared successfully !!'
            : 'Some cache steps failed — check the activity log.');

        return back();
    }
}
