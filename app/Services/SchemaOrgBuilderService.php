<?php

namespace App\Services;

use App\Models\AiVisibilitySetting;
use App\Models\SeoSetting;

/**
 * Turns a SeoSetting's AEO/GEO fields into a schema.org JSON-LD graph:
 * the base entity (Article/Product/Service/etc.), plus FAQPage and
 * HowTo nodes when the page has FAQ items or how-to steps, plus
 * author/reviewer authority signals. This is what answer engines and
 * LLM crawlers actually parse to extract a direct answer.
 */
class SchemaOrgBuilderService
{
    public function build(SeoSetting $page): ?array
    {
        if (! $page->enable_aeo && ! $page->enable_geo) {
            return null;
        }

        $context = AiVisibilitySetting::current()->default_schema_org_context ?: 'https://schema.org';
        $graph = [];

        $graph[] = $this->buildMainEntity($page);

        if ($page->enable_aeo && count($page->validFaqItems()) > 0) {
            $graph[] = $this->buildFaqPage($page);
        }

        if ($page->enable_aeo && count($page->validHowToSteps()) > 0) {
            $graph[] = $this->buildHowTo($page);
        }

        $graph = array_values(array_filter($graph));

        if (empty($graph)) {
            return null;
        }

        $document = [
            '@context' => $context,
            '@graph'   => $graph,
        ];

        // Per-page raw override, deep-merged last so an admin can add or
        // replace any property without needing a code change.
        if (! empty($page->schema_override_json)) {
            $document = array_replace_recursive($document, $page->schema_override_json);
        }

        return $document;
    }

    private function buildMainEntity(SeoSetting $page): array
    {
        $type = $page->schema_type ?: ($page->entity_type ?: 'Article');

        $node = [
            '@type' => $type,
            'name'  => $page->entity_name ?: $page->seo_title ?: $page->page_label,
            'url'   => url($page->page_url),
        ];

        if ($page->meta_description) {
            $node['description'] = $page->meta_description;
        }

        if ($page->answer_summary) {
            $node['abstract'] = $page->answer_summary;
        }

        if (! empty($page->primary_topics)) {
            $node['keywords'] = implode(', ', $page->primary_topics);
        }

        if ($page->author_name) {
            $node['author'] = array_filter([
                '@type'       => 'Person',
                'name'        => $page->author_name,
                'description' => $page->author_credentials,
                'sameAs'      => $page->author_social_profiles ?: null,
            ]);
        }

        if ($page->reviewed_by_name) {
            $node['reviewedBy'] = array_filter([
                '@type'       => 'Person',
                'name'        => $page->reviewed_by_name,
                'description' => $page->reviewed_by_credentials,
            ]);
        }

        if (count($page->validSources()) > 0) {
            $node['citation'] = collect($page->validSources())->map(fn ($s) => [
                '@type' => 'CreativeWork',
                'name'  => $s['title'],
                'url'   => $s['url'],
            ])->all();
        }

        if (! empty($page->geo_target_locations)) {
            $node['areaServed'] = $page->geo_target_locations;
        }

        return $node;
    }

    private function buildFaqPage(SeoSetting $page): array
    {
        return [
            '@type'      => 'FAQPage',
            'mainEntity' => collect($page->validFaqItems())->map(fn ($item) => [
                '@type'          => 'Question',
                'name'           => $item['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $item['answer'],
                ],
            ])->all(),
        ];
    }

    private function buildHowTo(SeoSetting $page): array
    {
        return [
            '@type' => 'HowTo',
            'name'  => $page->primary_question ?: $page->seo_title ?: $page->page_label,
            'step'  => collect($page->validHowToSteps())->map(fn ($step) => array_filter([
                '@type' => 'HowToStep',
                'name'  => $step['title'],
                'text'  => $step['body'],
                'image' => $step['image'] ?? null,
            ]))->all(),
        ];
    }
}
