<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SeoSetting;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SeoController extends Controller
{
    public $user;

    public function __construct(private ImageUploadService $imageUploadService)
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    /**
     * List every page's SEO record, seeding any system page that doesn't
     * exist yet from SeoSetting::DEFAULT_PAGES so the list is always
     * complete. Custom pages added by admins show up alongside them.
     */
    public function index()
    {
        $this->authorizeAbility('seo.view', 'view');

        foreach (SeoSetting::DEFAULT_PAGES as $key => $meta) {
            SeoSetting::firstOrCreate(
                ['page_key' => $key],
                ['page_label' => $meta['label'], 'page_url' => $meta['url'], 'is_default' => true]
            );
        }

        $pages = SeoSetting::orderBy('is_default', 'desc')->orderBy('page_label')->get();

        return view('backend.seo.index', compact('pages'));
    }

    public function create()
    {
        $this->authorizeAbility('seo.create', 'add');

        return view('backend.seo.create');
    }

    /**
     * Add a brand-new custom page. This is the "just add the page URL"
     * flow — anything that isn't one of the always-on system pages.
     */
    public function store(Request $request)
    {
        $this->authorizeAbility('seo.create', 'add');

        $request->merge(['page_url' => SeoSetting::normalizeUrl((string) $request->input('page_url'))]);

        $data = $this->validatePage($request, requireUrlFields: true);
        $data = array_merge($data, $this->extractAeoGeoData($request));

        $data['page_key'] = SeoSetting::generateUniqueKey($data['page_label']);
        $data['is_default'] = false;

        $page = SeoSetting::create($data);

        if ($request->hasFile('og_image')) {
            $page->og_image = $this->imageUploadService->replace($request->file('og_image'), 'seo', null);
        }
        if ($request->hasFile('twitter_image')) {
            $page->twitter_image = $this->imageUploadService->replace($request->file('twitter_image'), 'seo', null);
        }
        $page->save();

        ActivityLog::record('created', 'SEO', $page->id, "Added SEO page \"{$page->page_label}\" ({$page->page_url}).");

        session()->flash('success', 'New SEO page "'.$page->page_label.'" has been added !!');
        return redirect()->route('admin.seo.index');
    }

    public function edit(int $id)
    {
        $this->authorizeAbility('seo.edit', 'edit');

        $page = SeoSetting::findOrFail($id);
        return view('backend.seo.edit', compact('page'));
    }

    public function update(Request $request, int $id)
    {
        $this->authorizeAbility('seo.edit', 'edit');

        $page = SeoSetting::findOrFail($id);

        // System pages keep their fixed label/URL; only custom pages can
        // rename themselves or move to a different URL.
        $editingUrlFields = ! $page->is_default;

        if ($editingUrlFields) {
            $request->merge(['page_url' => SeoSetting::normalizeUrl((string) $request->input('page_url'))]);
        }

        $data = $this->validatePage($request, requireUrlFields: $editingUrlFields, ignoreId: $page->id);
        $data = array_merge($data, $this->extractAeoGeoData($request));

        if (! $editingUrlFields) {
            unset($data['page_label'], $data['page_url']);
        }

        $page->fill($data);

        if ($request->hasFile('og_image')) {
            $page->og_image = $this->imageUploadService->replace($request->file('og_image'), 'seo', $page->og_image);
        }

        if ($request->hasFile('twitter_image')) {
            $page->twitter_image = $this->imageUploadService->replace($request->file('twitter_image'), 'seo', $page->twitter_image);
        }

        $page->save();

        ActivityLog::record('updated', 'SEO', $page->id, "Updated SEO settings for \"{$page->page_label}\".");

        session()->flash('success', 'SEO settings for '.$page->page_label.' have been updated !!');
        return redirect()->route('admin.seo.index');
    }

    /**
     * Remove a custom page. System pages can't be deleted, only edited,
     * since the front-end always expects them to have a row.
     */
    public function destroy(int $id)
    {
        $this->authorizeAbility('seo.delete', 'delete');

        $page = SeoSetting::findOrFail($id);

        if ($page->is_default) {
            session()->flash('error', 'System pages can\'t be deleted, only edited.');
            return redirect()->route('admin.seo.index');
        }

        $label = $page->page_label;
        $page->delete();

        ActivityLog::record('deleted', 'SEO', $id, "Deleted SEO page \"{$label}\".");

        session()->flash('success', 'SEO page "'.$label.'" has been deleted.');
        return redirect()->route('admin.seo.index');
    }

    private function authorizeAbility(string $ability, string $verb): void
    {
        if (is_null($this->user) || ! $this->user->can($ability)) {
            abort(403, "Sorry !! You are unauthorized to {$verb} SEO settings !");
        }
    }

    private function validatePage(Request $request, bool $requireUrlFields, ?int $ignoreId = null): array
    {
        $rules = [
            // Classic SEO
            'seo_title'            => 'nullable|string|max:255',
            'meta_description'     => 'nullable|string|max:500',
            'focus_keyword'        => 'nullable|string|max:150',
            'canonical_url'        => 'nullable|url|max:255',
            'robots_meta'          => 'nullable|string|max:100',
            'og_title'             => 'nullable|string|max:255',
            'og_description'       => 'nullable|string|max:500',
            'og_image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'twitter_title'        => 'nullable|string|max:255',
            'twitter_description'  => 'nullable|string|max:500',
            'twitter_image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            // AEO
            'enable_aeo'            => 'nullable|boolean',
            'primary_question'      => 'nullable|string|max:255',
            'answer_summary'        => 'nullable|string|max:2000',
            'key_takeaways'         => 'nullable|string|max:4000',   // one per line, split below
            'faq_items.*.question'  => 'nullable|string|max:500',
            'faq_items.*.answer'    => 'nullable|string|max:2000',
            'how_to_steps.*.title'  => 'nullable|string|max:255',
            'how_to_steps.*.body'   => 'nullable|string|max:2000',
            'how_to_steps.*.image'  => 'nullable|string|max:500',
            'json_ld'               => ['nullable', 'json'],

            // GEO / LLM
            'enable_geo'                 => 'nullable|boolean',
            'llm_citation_allowed'       => 'nullable|boolean',
            'llm_training_allowed'       => 'nullable|boolean',
            'entity_type'                => 'nullable|string|max:100',
            'entity_name'                => 'nullable|string|max:255',
            'primary_topics'             => 'nullable|string|max:1000', // comma-separated, split below
            'author_name'                => 'nullable|string|max:150',
            'author_credentials'         => 'nullable|string|max:255',
            'author_social_profiles'     => 'nullable|string|max:1000', // comma-separated URLs
            'reviewed_by_name'           => 'nullable|string|max:150',
            'reviewed_by_credentials'    => 'nullable|string|max:255',
            'sources_and_references.*.title' => 'nullable|string|max:255',
            'sources_and_references.*.url'   => 'nullable|url|max:500',
            'target_audience'            => 'nullable|string|max:255',
            'geo_target_locations'       => 'nullable|string|max:1000', // comma-separated
            'content_quality_score'      => 'nullable|integer|min:0|max:100',
            'schema_type'                => 'nullable|string|max:50',
        ];

        if ($requireUrlFields) {
            $rules['page_label'] = 'required|string|max:150';
            $rules['page_url'] = [
                'required', 'string', 'max:255',
                Rule::unique('seo_settings', 'page_url')->ignore($ignoreId),
            ];
        }

        return $request->validate($rules);
    }

    /**
     * Pulls the AEO/GEO fields out of the request and reshapes them into
     * the arrays the model's JSON casts expect: repeaters keep only rows
     * that actually have content, and comma/line-separated text inputs
     * become clean string arrays.
     */
    private function extractAeoGeoData(Request $request): array
    {
        return [
            'enable_aeo'             => $request->boolean('enable_aeo'),
            'enable_geo'             => $request->boolean('enable_geo'),
            'llm_citation_allowed'   => $request->boolean('llm_citation_allowed'),
            'llm_training_allowed'   => $request->boolean('llm_training_allowed'),
            'json_ld'                => $request->filled('json_ld') ? json_decode($request->json_ld, true) : null,

            'primary_question'       => $request->input('primary_question'),
            'answer_summary'         => $request->input('answer_summary'),
            'key_takeaways'          => $this->linesToArray($request->input('key_takeaways')),

            'faq_items'              => $this->cleanRepeater($request->input('faq_items', []), ['question', 'answer']),
            'how_to_steps'           => $this->cleanRepeater($request->input('how_to_steps', []), ['title', 'body'], ['image']),
            'sources_and_references' => $this->cleanRepeater($request->input('sources_and_references', []), ['title', 'url']),

            'entity_type'             => $request->input('entity_type'),
            'entity_name'             => $request->input('entity_name'),
            'primary_topics'          => $this->commaToArray($request->input('primary_topics')),
            'author_name'             => $request->input('author_name'),
            'author_credentials'      => $request->input('author_credentials'),
            'author_social_profiles'  => $this->commaToArray($request->input('author_social_profiles')),
            'reviewed_by_name'        => $request->input('reviewed_by_name'),
            'reviewed_by_credentials' => $request->input('reviewed_by_credentials'),
            'target_audience'         => $request->input('target_audience'),
            'geo_target_locations'    => $this->commaToArray($request->input('geo_target_locations')),
            'content_quality_score'   => $request->input('content_quality_score') !== '' ? $request->input('content_quality_score') : null,
            'schema_type'             => $request->input('schema_type') ?: 'Article',
        ];
    }

    private function linesToArray(?string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    private function commaToArray(?string $value): array
    {
        return collect(explode(',', (string) $value))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Keeps only repeater rows where every required field is filled in,
     * so a half-empty row left over from the form doesn't get saved.
     */
    private function cleanRepeater(array $rows, array $requiredFields, array $optionalFields = []): array
    {
        return collect($rows)
            ->filter(function ($row) use ($requiredFields) {
                foreach ($requiredFields as $field) {
                    if (blank($row[$field] ?? null)) {
                        return false;
                    }
                }
                return true;
            })
            ->map(function ($row) use ($requiredFields, $optionalFields) {
                $clean = [];
                foreach (array_merge($requiredFields, $optionalFields) as $field) {
                    $clean[$field] = $row[$field] ?? null;
                }
                return $clean;
            })
            ->values()
            ->all();
    }
}
