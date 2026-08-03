@props([
    'seoTitle' => null,
    'metaDescription' => null,
    'focusKeyword' => null,
    'canonicalUrl' => null,
    'ogTitle' => null,
    'ogDescription' => null,
    'ogImage' => null,
    'showOgImage' => true,
    // Full mode adds focus keyword, canonical URL, and OG title/description
    // (used by Blogs). Basic mode is just title + meta description + OG image
    // (used by Services, Projects). Careers has no og_image column, so it
    // passes show-og-image="false".
    'full' => false,
])

<div class="row g-3">
    <div class="col-md-6">
        <x-admin.input name="seo_title" label="SEO Title" :value="$seoTitle" help="Shown in search engine results and browser tabs." />
    </div>

    @if ($full)
        <div class="col-md-6">
            <x-admin.input name="focus_keyword" label="Focus Keyword" :value="$focusKeyword" />
        </div>
        <div class="col-md-6">
            <x-admin.input name="canonical_url" label="Canonical URL" type="url" :value="$canonicalUrl" />
        </div>
        <div class="col-md-6">
            <x-admin.image-upload name="og_image" label="OG Image" :existing="$ogImage" />
        </div>
        <div class="col-md-6">
            <x-admin.input name="og_title" label="OG Title" :value="$ogTitle" help="Falls back to SEO title if left blank." />
        </div>
        <div class="col-md-6">
            <label class="form-label small fw-medium">OG Description</label>
            <textarea name="og_description" class="form-control" rows="2" maxlength="500">{{ old('og_description', $ogDescription) }}</textarea>
        </div>
    @else
        @if ($showOgImage)
            <div class="col-md-6">
                <x-admin.image-upload name="og_image" label="OG Image" :existing="$ogImage" />
            </div>
        @endif
    @endif

    <div class="col-12">
        <label class="form-label small fw-medium">Meta Description</label>
        <textarea name="meta_description" class="form-control" rows="2" maxlength="500">{{ old('meta_description', $metaDescription) }}</textarea>
        <div class="form-text">Recommended length: 150–160 characters.</div>
    </div>
</div>
