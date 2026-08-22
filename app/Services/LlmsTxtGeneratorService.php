<?php

namespace App\Services;

use App\Models\AiVisibilitySetting;
use App\Models\SeoSetting;

/**
 * Builds the content served at /llms.txt — the emerging convention
 * (llmstxt.org) for telling LLMs and answer engines which pages on a
 * site are worth reading and what each one is about, in plain text
 * they can parse cheaply without rendering HTML.
 */
class LlmsTxtGeneratorService
{
    public function generate(): string
    {
        $settings = AiVisibilitySetting::current();

        $lines = [];
        $lines[] = '# '.config('app.name');
        $lines[] = '';

        if ($settings->llms_txt_intro) {
            $lines[] = trim($settings->llms_txt_intro);
            $lines[] = '';
        }

        $lines[] = '> Generated automatically from this site\'s SEO / AEO / GEO settings.';
        $lines[] = '';

        $pages = SeoSetting::llmCitable()->orderBy('page_label')->get();

        if ($pages->isEmpty()) {
            $lines[] = 'No pages are currently marked as citable.';
            return implode("\n", $lines);
        }

        $lines[] = '## Pages';
        $lines[] = '';

        foreach ($pages as $page) {
            $title = $page->seo_title ?: $page->page_label;
            $url = url($page->page_url);
            $summary = $page->answer_summary ?: $page->meta_description;

            $line = "- [{$title}]({$url})";
            if ($summary) {
                $line .= ': '.str($summary)->limit(200);
            }

            $lines[] = $line;
        }

        return implode("\n", $lines)."\n";
    }
}
