<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AiCitationLog;
use App\Models\AiVisibilitySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiVisibilityController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('ai-visibility.manage')) {
            abort(403, 'Sorry !! You are unauthorized to view AI Visibility settings !');
        }

        $settings = AiVisibilitySetting::current();
        $sourceStats = AiCitationLog::countsBySource(30);
        $topPages = AiCitationLog::topPages(30);

        return view('backend.ai-visibility.index', compact('settings', 'sourceStats', 'topPages'));
    }

    public function update(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('ai-visibility.manage')) {
            abort(403, 'Sorry !! You are unauthorized to edit AI Visibility settings !');
        }

        $data = $request->validate([
            'aeo_enabled'                => 'nullable|boolean',
            'geo_enabled'                => 'nullable|boolean',
            'llm_crawlers_allowed'       => 'nullable|string', // one bot name per line
            'llm_training_policy'        => 'required|in:allow,disallow,selective',
            'llms_txt_enabled'           => 'nullable|boolean',
            'llms_txt_intro'             => 'nullable|string|max:2000',
            'robots_txt_ai_rules'        => 'nullable|string|max:2000',
            'default_schema_org_context' => 'nullable|string|max:255',
            'citation_tracking_enabled'  => 'nullable|boolean',
        ]);

        $data['aeo_enabled'] = $request->boolean('aeo_enabled');
        $data['geo_enabled'] = $request->boolean('geo_enabled');
        $data['llms_txt_enabled'] = $request->boolean('llms_txt_enabled');
        $data['citation_tracking_enabled'] = $request->boolean('citation_tracking_enabled');

        $data['llm_crawlers_allowed'] = collect(preg_split('/\r\n|\r|\n/', (string) $data['llm_crawlers_allowed']))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        $settings = AiVisibilitySetting::current();
        $settings->update($data);

        ActivityLog::record('updated', 'AI Visibility', $settings->id, 'Updated site-wide AEO/GEO/LLM settings.');

        session()->flash('success', 'AI Visibility settings have been updated !!');
        return redirect()->route('admin.ai-visibility.index');
    }
}
