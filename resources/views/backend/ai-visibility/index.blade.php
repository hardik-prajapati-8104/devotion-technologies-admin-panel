@extends('backend.layouts.master')

@section('title', 'AI Visibility (AEO / GEO / LLM)')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">AI Visibility</li>
        </ol>
    </nav>
    <h4>AI Visibility</h4>
    <p class="subtitle">Site-wide AEO/GEO/LLM defaults. Per-page overrides live on each page's SEO edit screen.</p>
@endsection

@section('admin-content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.ai-visibility.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="aeo_enabled" name="aeo_enabled" value="1"
                                    {{ old('aeo_enabled', $settings->aeo_enabled) ? 'checked' : '' }}>
                                <label class="form-check-label" for="aeo_enabled">Enable AEO site-wide</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="geo_enabled" name="geo_enabled" value="1"
                                    {{ old('geo_enabled', $settings->geo_enabled) ? 'checked' : '' }}>
                                <label class="form-check-label" for="geo_enabled">Enable GEO site-wide</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="citation_tracking_enabled" name="citation_tracking_enabled" value="1"
                                    {{ old('citation_tracking_enabled', $settings->citation_tracking_enabled) ? 'checked' : '' }}>
                                <label class="form-check-label" for="citation_tracking_enabled">Track AI crawler visits &amp; AI-referred traffic</label>
                            </div>
                            <div class="form-text">Powers the stats panel on the right. Logs a row per detected visit — see README for converting this to a queued job on high-traffic sites.</div>
                        </div>

                        <div class="col-12"><hr></div>

                        <div class="col-12">
                            <label class="form-label small fw-medium">LLM Training Policy</label>
                            <select name="llm_training_policy" class="form-select" style="max-width: 320px;">
                                @foreach (\App\Models\AiVisibilitySetting::TRAINING_POLICIES as $policy)
                                    <option value="{{ $policy }}" {{ old('llm_training_policy', $settings->llm_training_policy) === $policy ? 'selected' : '' }}>
                                        {{ ucfirst($policy) }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <strong>Allow</strong>: every listed bot can train on the whole site.
                                <strong>Disallow</strong>: every listed bot is blocked site-wide.
                                <strong>Selective</strong>: respects each page's own "Allow LLMs to train" toggle.
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-medium">AI Crawlers (one per line)</label>
                            <textarea name="llm_crawlers_allowed" class="form-control" rows="6">{{ old('llm_crawlers_allowed', implode("\n", $settings->llm_crawlers_allowed ?? [])) }}</textarea>
                            <div class="form-text">These are the bot user-agents robots.txt rules get generated for.</div>
                        </div>

                        <div class="col-12"><hr></div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="llms_txt_enabled" name="llms_txt_enabled" value="1"
                                    {{ old('llms_txt_enabled', $settings->llms_txt_enabled) ? 'checked' : '' }}>
                                <label class="form-check-label" for="llms_txt_enabled">Serve /llms.txt</label>
                            </div>
                            <div class="form-text">Auto-lists every page with GEO enabled and citation allowed. Preview: <a href="{{ url('/llms.txt') }}" target="_blank">{{ url('/llms.txt') }}</a></div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium">llms.txt intro text <span class="text-muted">(optional, shown above the page list)</span></label>
                            <textarea name="llms_txt_intro" class="form-control" rows="3" maxlength="2000">{{ old('llms_txt_intro', $settings->llms_txt_intro) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-medium">Extra robots.txt AI rules <span class="text-muted">(optional, raw text appended as-is)</span></label>
                            <textarea name="robots_txt_ai_rules" class="form-control" rows="3" maxlength="2000">{{ old('robots_txt_ai_rules', $settings->robots_txt_ai_rules) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-medium">Default schema.org context</label>
                            <input type="text" name="default_schema_org_context" class="form-control" style="max-width: 320px;"
                                value="{{ old('default_schema_org_context', $settings->default_schema_org_context) }}">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="card-title">AI Activity — last 30 days</h6>
                @if ($sourceStats->isEmpty())
                    <p class="text-muted small mb-0">No AI crawler visits or AI-referred traffic logged yet.</p>
                @else
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr><th>Source</th><th>Type</th><th class="text-end">Visits</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($sourceStats as $row)
                                <tr>
                                    <td>{{ $row->source }}</td>
                                    <td><span class="badge bg-{{ $row->type === 'crawler' ? 'secondary' : 'info text-dark' }}">{{ $row->type }}</span></td>
                                    <td class="text-end">{{ $row->total }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Most-visited pages by AI — last 30 days</h6>
                @if ($topPages->isEmpty())
                    <p class="text-muted small mb-0">No data yet.</p>
                @else
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr><th>Page</th><th class="text-end">Visits</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($topPages as $row)
                                <tr>
                                    <td><code>{{ $row->url_path }}</code></td>
                                    <td class="text-end">{{ $row->total }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
