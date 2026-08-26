{{--
    Front-end SEO meta tags.

    Include this in your main public layout's <head>, e.g.:
        @include('partials.seo-meta')

    Or pass a specific record (e.g. for a blog post, service, etc. that
    manages its own SEO fields on its own model):
        @include('partials.seo-meta', ['seo' => $service])
--}}
@php
    $seo = $seo ?? app(\App\Services\SeoResolverService::class)->resolve(request());
    $title = $seo?->seo_title ?: config('app.name');
    $description = $seo?->meta_description;
    $ogTitle = $seo?->og_title ?: $title;
    $ogDescription = $seo?->og_description ?: $description;
    $ogImage = $seo?->og_image ? asset('storage/'.$seo->og_image) : null;
    $twitterTitle = $seo?->twitter_title ?: $ogTitle;
    $twitterDescription = $seo?->twitter_description ?: $ogDescription;
    $twitterImage = $seo?->twitter_image ? asset('storage/'.$seo->twitter_image) : $ogImage;
@endphp
<title>{{ $title }}</title>
@if ($description)
    <meta name="description" content="{{ $description }}">
@endif
@if ($seo?->focus_keyword)
    <meta name="keywords" content="{{ $seo->focus_keyword }}">
@endif
    <meta name="robots" content="{{ $seo?->robots_meta ?: 'index, follow' }}">
@if ($seo?->canonical_url)
    <link rel="canonical" href="{{ $seo->canonical_url }}">
@else
    <link rel="canonical" href="{{ url()->current() }}">
@endif
    <meta property="og:title" content="{{ $ogTitle }}">
@if ($ogDescription)
    <meta property="og:description" content="{{ $ogDescription }}">
@endif
@if ($ogImage)
    <meta property="og:image" content="{{ $ogImage }}">
@endif
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $twitterTitle }}">
@if ($twitterDescription)
    <meta name="twitter:description" content="{{ $twitterDescription }}">
@endif
@if ($twitterImage)
    <meta name="twitter:image" content="{{ $twitterImage }}">
@endif
@if ($seo instanceof \App\Models\SeoSetting && $seo->enable_geo)
    <meta name="llm-citation" content="{{ $seo->llm_citation_allowed ? 'allowed' : 'disallowed' }}">
    <meta name="llm-training" content="{{ $seo->llm_training_allowed ? 'allowed' : 'disallowed' }}">
@endif
@if(!empty($seo?->json_ld))
    <script type="application/ld+json">
        {!! json_encode(
            $seo->json_ld,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        ) !!}
    </script>
@endif


