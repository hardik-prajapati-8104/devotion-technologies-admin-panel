<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SeoSetting extends Model
{
    protected $fillable = [
        'page_key', 'page_label', 'page_url', 'is_default',
        'seo_title', 'meta_description', 'focus_keyword',
        'canonical_url', 'robots_meta', 'og_title', 'og_description', 'og_image',
        'twitter_title', 'twitter_description', 'twitter_image',
    ];

    protected $casts = [
        'is_default' => 'boolean',
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

    public function scopeDefaultPages($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeCustomPages($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Normalize any URL/path into the '/segment/segment' form stored in
     * page_url, so lookups work no matter how it was typed — a full URL
     * with domain, with/without a leading slash, with/without a trailing
     * slash, all resolve to the same stored value.
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
}
