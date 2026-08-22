<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    protected $fillable = [
        'page_key', 'page_label', 'seo_title', 'meta_description', 'focus_keyword',
        'canonical_url', 'robots_meta', 'og_title', 'og_description', 'og_image',
        'twitter_title', 'twitter_description', 'twitter_image',
    ];

    /**
     * Pages that always exist in the site structure and should always
     * have a row here, even before an admin has customized them.
     */
    public const DEFAULT_PAGES = [
        'home'     => 'Home Page',
        'about'    => 'About Us Page',
        'services' => 'Services Listing Page',
        'projects' => 'Projects Listing Page',
        'blog'     => 'Blog Listing Page',
        'careers'  => 'Careers Listing Page',
        'contact'  => 'Contact Page',
    ];
}
