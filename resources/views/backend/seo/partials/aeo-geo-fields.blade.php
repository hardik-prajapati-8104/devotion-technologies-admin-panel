{{--
    AEO / GEO / LLM Visibility fields, shared by create.blade.php and
    edit.blade.php. Expects an optional $page (SeoSetting|null) —
    pass nothing (or null) on the create form.

    Repeaters (FAQ items, sources, how-to steps) are plain vanilla JS —
    no build step, no Alpine/Livewire dependency — so they work
    regardless of what your project already uses.
--}}
@php
    $page = $page ?? null;
    $faqItems = old('faq_items', $page->faq_items ?? []);
    $sources = old('sources_and_references', $page->sources_and_references ?? []);
    $steps = old('how_to_steps', $page->how_to_steps ?? []);
    $keyTakeaways = old('key_takeaways', $page ? implode("\n", $page->key_takeaways ?? []) : '');
    $primaryTopics = old('primary_topics', $page ? implode(', ', $page->primary_topics ?? []) : '');
    $authorSocials = old('author_social_profiles', $page ? implode(', ', $page->author_social_profiles ?? []) : '');
    $geoLocations = old('geo_target_locations', $page ? implode(', ', $page->geo_target_locations ?? []) : '');
@endphp

<div class="d-flex align-items-center gap-2 mt-5 mb-3">
    <h5 class="mb-0">AEO — Answer Engine Optimization</h5>
    <span class="text-muted small">Helps this page get pulled into direct answers (Google AI Overviews, voice search, etc.)</span>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="enable_aeo" name="enable_aeo" value="1"
                {{ old('enable_aeo', $page->enable_aeo ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="enable_aeo">Enable AEO for this page</label>
        </div>
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-medium">Content Quality Score <span class="text-muted">(0–100, optional)</span></label>
        <input type="number" min="0" max="100" name="content_quality_score" class="form-control"
            value="{{ old('content_quality_score', $page->content_quality_score ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-medium">Primary Question</label>
        <input type="text" name="primary_question" class="form-control" placeholder="What question does this page answer?"
            value="{{ old('primary_question', $page->primary_question ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-medium">Schema Type</label>
        <select name="schema_type" class="form-select">
            @foreach (\App\Models\SeoSetting::ENTITY_TYPES as $type)
                <option value="{{ $type }}" {{ old('schema_type', $page->schema_type ?? 'Article') === $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-12">
        <label class="form-label small fw-medium">Answer Summary <span class="text-muted">(1–3 direct sentences answering the primary question)</span></label>
        <textarea name="answer_summary" class="form-control" rows="3" maxlength="2000">{{ old('answer_summary', $page->answer_summary ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label small fw-medium">Key Takeaways <span class="text-muted">(one per line)</span></label>
        <textarea name="key_takeaways" class="form-control" rows="3" maxlength="4000">{{ $keyTakeaways }}</textarea>
    </div>
</div>

{{-- FAQ repeater --}}
<div class="mt-4">
    <label class="form-label small fw-medium d-block">FAQ Items <span class="text-muted">(powers FAQPage schema)</span></label>
    <div id="faq-repeater">
        @forelse ($faqItems as $i => $item)
            <div class="row g-2 mb-2 repeater-row">
                <div class="col-md-5">
                    <input type="text" name="faq_items[{{ $i }}][question]" class="form-control" placeholder="Question" value="{{ $item['question'] ?? '' }}">
                </div>
                <div class="col-md-6">
                    <input type="text" name="faq_items[{{ $i }}][answer]" class="form-control" placeholder="Answer" value="{{ $item['answer'] ?? '' }}">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger w-100 remove-row"><i class="bi bi-x"></i></button>
                </div>
            </div>
        @empty
        @endforelse
    </div>
    <button type="button" class="btn btn-sm btn-outline-secondary" data-add-row="faq-repeater" data-template="faq-row-template">
        <i class="bi bi-plus"></i> Add FAQ item
    </button>
</div>

<div class="d-flex align-items-center gap-2 mt-5 mb-3">
    <h5 class="mb-0">GEO &amp; LLM Visibility</h5>
    <span class="text-muted small">Controls how generative engines and LLM crawlers cite and train on this page</span>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="enable_geo" name="enable_geo" value="1"
                {{ old('enable_geo', $page->enable_geo ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="enable_geo">Enable GEO for this page</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="llm_citation_allowed" name="llm_citation_allowed" value="1"
                {{ old('llm_citation_allowed', $page->llm_citation_allowed ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="llm_citation_allowed">Allow LLMs to cite this page</label>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="llm_training_allowed" name="llm_training_allowed" value="1"
                {{ old('llm_training_allowed', $page->llm_training_allowed ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="llm_training_allowed">Allow LLMs to train on this page</label>
        </div>
        <div class="form-text">Only enforced when the site-wide LLM training policy is "Selective" (see AI Visibility settings).</div>
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-medium">Entity Type</label>
        <select name="entity_type" class="form-select">
            <option value="">—</option>
            @foreach (\App\Models\SeoSetting::ENTITY_TYPES as $type)
                <option value="{{ $type }}" {{ old('entity_type', $page->entity_type ?? '') === $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-medium">Entity Name</label>
        <input type="text" name="entity_name" class="form-control" value="{{ old('entity_name', $page->entity_name ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-medium">Primary Topics <span class="text-muted">(comma-separated)</span></label>
        <input type="text" name="primary_topics" class="form-control" placeholder="e.g. laravel hosting, seo automation" value="{{ $primaryTopics }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-medium">Target Audience</label>
        <input type="text" name="target_audience" class="form-control" placeholder="e.g. small business owners" value="{{ old('target_audience', $page->target_audience ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-medium">Author Name</label>
        <input type="text" name="author_name" class="form-control" value="{{ old('author_name', $page->author_name ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-medium">Author Credentials</label>
        <input type="text" name="author_credentials" class="form-control" placeholder="e.g. 10+ years in SEO" value="{{ old('author_credentials', $page->author_credentials ?? '') }}">
    </div>
    <div class="col-12">
        <label class="form-label small fw-medium">Author Social / Profile URLs <span class="text-muted">(comma-separated)</span></label>
        <input type="text" name="author_social_profiles" class="form-control" value="{{ $authorSocials }}">
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-medium">Reviewed By</label>
        <input type="text" name="reviewed_by_name" class="form-control" value="{{ old('reviewed_by_name', $page->reviewed_by_name ?? '') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-medium">Reviewer Credentials</label>
        <input type="text" name="reviewed_by_credentials" class="form-control" value="{{ old('reviewed_by_credentials', $page->reviewed_by_credentials ?? '') }}">
    </div>

    <div class="col-12">
        <label class="form-label small fw-medium">GEO Target Locations <span class="text-muted">(comma-separated, for local GEO)</span></label>
        <input type="text" name="geo_target_locations" class="form-control" placeholder="e.g. Surat, Ahmedabad, Gujarat" value="{{ $geoLocations }}">
    </div>
</div>

{{-- Sources repeater --}}
<div class="mt-4">
    <label class="form-label small fw-medium d-block">Sources &amp; References</label>
    <div id="sources-repeater">
        @forelse ($sources as $i => $src)
            <div class="row g-2 mb-2 repeater-row">
                <div class="col-md-5">
                    <input type="text" name="sources_and_references[{{ $i }}][title]" class="form-control" placeholder="Source title" value="{{ $src['title'] ?? '' }}">
                </div>
                <div class="col-md-6">
                    <input type="url" name="sources_and_references[{{ $i }}][url]" class="form-control" placeholder="https://..." value="{{ $src['url'] ?? '' }}">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger w-100 remove-row"><i class="bi bi-x"></i></button>
                </div>
            </div>
        @empty
        @endforelse
    </div>
    <button type="button" class="btn btn-sm btn-outline-secondary" data-add-row="sources-repeater" data-template="source-row-template">
        <i class="bi bi-plus"></i> Add source
    </button>
</div>

{{-- How-to steps repeater --}}
<div class="mt-4 mb-3">
    <label class="form-label small fw-medium d-block">How-To Steps <span class="text-muted">(only needed for tutorial/guide pages — powers HowTo schema)</span></label>
    <div id="howto-repeater">
        @forelse ($steps as $i => $step)
            <div class="row g-2 mb-2 repeater-row">
                <div class="col-md-3">
                    <input type="text" name="how_to_steps[{{ $i }}][title]" class="form-control" placeholder="Step title" value="{{ $step['title'] ?? '' }}">
                </div>
                <div class="col-md-7">
                    <input type="text" name="how_to_steps[{{ $i }}][body]" class="form-control" placeholder="Step description" value="{{ $step['body'] ?? '' }}">
                </div>
                <div class="col-md-1">
                    <input type="text" name="how_to_steps[{{ $i }}][image]" class="form-control" placeholder="Image URL (optional)" value="{{ $step['image'] ?? '' }}">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger w-100 remove-row"><i class="bi bi-x"></i></button>
                </div>
            </div>
        @empty
        @endforelse
    </div>
    <button type="button" class="btn btn-sm btn-outline-secondary" data-add-row="howto-repeater" data-template="howto-row-template">
        <i class="bi bi-plus"></i> Add step
    </button>
</div>

{{-- Hidden templates cloned by the JS below --}}
<template id="faq-row-template">
    <div class="row g-2 mb-2 repeater-row">
        <div class="col-md-5"><input type="text" name="faq_items[__INDEX__][question]" class="form-control" placeholder="Question"></div>
        <div class="col-md-6"><input type="text" name="faq_items[__INDEX__][answer]" class="form-control" placeholder="Answer"></div>
        <div class="col-md-1"><button type="button" class="btn btn-outline-danger w-100 remove-row"><i class="bi bi-x"></i></button></div>
    </div>
</template>
<template id="source-row-template">
    <div class="row g-2 mb-2 repeater-row">
        <div class="col-md-5"><input type="text" name="sources_and_references[__INDEX__][title]" class="form-control" placeholder="Source title"></div>
        <div class="col-md-6"><input type="url" name="sources_and_references[__INDEX__][url]" class="form-control" placeholder="https://..."></div>
        <div class="col-md-1"><button type="button" class="btn btn-outline-danger w-100 remove-row"><i class="bi bi-x"></i></button></div>
    </div>
</template>
<template id="howto-row-template">
    <div class="row g-2 mb-2 repeater-row">
        <div class="col-md-3"><input type="text" name="how_to_steps[__INDEX__][title]" class="form-control" placeholder="Step title"></div>
        <div class="col-md-7"><input type="text" name="how_to_steps[__INDEX__][body]" class="form-control" placeholder="Step description"></div>
        <div class="col-md-1"><input type="text" name="how_to_steps[__INDEX__][image]" class="form-control" placeholder="Image URL"></div>
        <div class="col-md-1"><button type="button" class="btn btn-outline-danger w-100 remove-row"><i class="bi bi-x"></i></button></div>
    </div>
</template>

@once
<script>
document.addEventListener('DOMContentLoaded', function () {
    let counter = Date.now();

    document.querySelectorAll('[data-add-row]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const container = document.getElementById(btn.dataset.addRow);
            const template = document.getElementById(btn.dataset.template);
            const html = template.innerHTML.replace(/__INDEX__/g, 'new_' + (counter++));
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            container.appendChild(wrapper.firstElementChild);
        });
    });

    document.body.addEventListener('click', function (e) {
        if (e.target.closest('.remove-row')) {
            e.target.closest('.repeater-row').remove();
        }
    });
});
</script>
@endonce
