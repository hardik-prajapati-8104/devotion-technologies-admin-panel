<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SeoSetting extends Model
{
    protected $fillable = [
        'page_key', 'page_label', 'page_url', 'is_default',
        // Classic SEO
        'seo_title', 'meta_description', 'focus_keyword',
        'canonical_url', 'robots_meta', 'og_title', 'og_description', 'og_image',
        'twitter_title', 'twitter_description', 'twitter_image',
        // AEO / GEO / LLM
        'enable_aeo', 'enable_geo',
        'primary_question', 'answer_summary', 'key_takeaways', 'faq_items', 'how_to_steps',
        'llm_citation_allowed', 'llm_training_allowed',
        'entity_type', 'entity_name', 'primary_topics',
        'author_name', 'author_credentials', 'author_social_profiles',
        'reviewed_by_name', 'reviewed_by_credentials',
        'sources_and_references', 'target_audience', 'geo_target_locations',
        'content_quality_score', 'schema_type', 'schema_override_json',
    ];

    protected $casts = [
        'is_default'                => 'boolean',
        'enable_aeo'                 => 'boolean',
        'enable_geo'                  => 'boolean',
        'llm_citation_allowed'        => 'boolean',
        'llm_training_allowed'        => 'boolean',
        'content_quality_score'       => 'integer',
        'key_takeaways'               => 'array',
        'faq_items'                   => 'array',
        'how_to_steps'                => 'array',
        'primary_topics'              => 'array',
        'author_social_profiles'      => 'array',
        'sources_and_references'      => 'array',
        'geo_target_locations'        => 'array',
        'schema_override_json'        => 'array',
    ];

    /**
     * Pages that always exist in the site structure and should always
     * have a row here, even before an admin has customized them.
     * `url` is the front-end path used to match this record on the
     * public site — see SeoSetting::forUrl().
     */
    public const DEFAULT_PAGES = [
        'home'     => ['label' => 'Home Page',            'url' => '/'],
        'about'    => ['label' => 'About Us Page',         'url' => '/about-us'],
        'services' => ['label' => 'Services Listing Page', 'url' => '/services'],
        'projects' => ['label' => 'Projects Listing Page', 'url' => '/projects'],
        'blog'     => ['label' => 'Blog Listing Page',     'url' => '/blog'],
        'careers'  => ['label' => 'Careers Listing Page',  'url' => '/careers'],
        'contact'  => ['label' => 'Contact Page',          'url' => '/contact'],
    ];

    /**
     * Entity types an editor can pick for schema.org / GEO purposes.
     */
    public const ENTITY_TYPES = ['Article', 'Product', 'Service', 'Organization', 'Person', 'FAQPage', 'LocalBusiness'];

    public function scopeDefaultPages($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeCustomPages($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Pages eligible to be listed in llms.txt / cited by LLMs: AEO/GEO
     * turned on for the page and citation explicitly allowed.
     */
    public function scopeLlmCitable($query)
    {
        return $query->where('enable_geo', true)->where('llm_citation_allowed', true);
    }

    /**
     * Normalize any URL/path into the '/segment/segment' form stored in
     * page_url, so lookups work no matter how it was typed.
     */
    public static function normalizeUrl(string $url): string
    {
        $path = parse_url(trim($url), PHP_URL_PATH) ?? $url;
        $path = '/'.trim($path, '/');

        return $path === '' ? '/' : $path;
    }

    /**
     * Generate a unique, URL-safe page_key from a label. Used when an
     * admin adds a new custom page without setting one manually.
     */
    public static function generateUniqueKey(string $label): string
    {
        $base = Str::slug($label, '_') ?: 'page';
        $key = $base;
        $i = 1;

        while (static::where('page_key', $key)->exists()) {
            $key = $base.'_'.(++$i);
        }

        return $key;
    }

    /**
     * Look up the SEO record for a given front-end URL/path, for use in
     * the public site's <head>. Returns null if nothing was configured
     * for that page yet.
     */
    public static function forUrl(string $url): ?self
    {
        return static::where('page_url', static::normalizeUrl($url))->first();
    }

    /**
     * True FAQ items with both a question and an answer filled in —
     * filters out empty repeater rows submitted from the admin form.
     */
    public function validFaqItems(): array
    {
        return collect($this->faq_items ?? [])
            ->filter(fn ($item) => filled($item['question'] ?? null) && filled($item['answer'] ?? null))
            ->values()
            ->all();
    }

    public function validHowToSteps(): array
    {
        return collect($this->how_to_steps ?? [])
            ->filter(fn ($step) => filled($step['title'] ?? null) && filled($step['body'] ?? null))
            ->values()
            ->all();
    }

    public function validSources(): array
    {
        return collect($this->sources_and_references ?? [])
            ->filter(fn ($src) => filled($src['title'] ?? null) && filled($src['url'] ?? null))
            ->values()
            ->all();
    }
}
