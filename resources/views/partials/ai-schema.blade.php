{{--
    Renders the AEO/GEO JSON-LD graph for the current page (or a
    specific $seo record passed in). Include right after seo-meta:

        @include('partials.seo-meta')
        @include('partials.ai-schema')

    Or for a page that manages its own SEO on its own model:

        @include('partials.ai-schema', ['seo' => $service])
--}}
@php
    $seo = $seo ?? app(\App\Services\SeoResolverService::class)->resolve(request());
    $schema = $seo instanceof \App\Models\SeoSetting
        ? app(\App\Services\SchemaOrgBuilderService::class)->build($seo)
        : null;
@endphp

@if ($schema)
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif
