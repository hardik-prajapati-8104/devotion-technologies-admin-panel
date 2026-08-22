@extends('backend.layouts.master')

@section('title', 'Edit SEO — '.$page->page_label)

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.seo.index') }}">SEO Management</a></li>
            <li class="breadcrumb-item active">{{ $page->page_label }}</li>
        </ol>
    </nav>
    <h4>{{ $page->page_label }}</h4>
    @if ($page->is_default)
        <span class="badge bg-secondary">System Page</span>
    @else
        <span class="badge bg-info text-dark">Custom Page</span>
    @endif
@endsection

@section('admin-content')
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.seo.update', $page->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if ($page->is_default)
                <div class="alert alert-light border small mb-3">
                    This is a system page (<code>{{ $page->page_url }}</code>). Its label and URL are fixed, but you can still edit everything below.
                </div>
            @else
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-medium">Page Label <span class="text-danger">*</span></label>
                        <input type="text" name="page_label" class="form-control @error('page_label') is-invalid @enderror"
                            value="{{ old('page_label', $page->page_label) }}" required>
                        @error('page_label') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-medium">Page URL <span class="text-danger">*</span></label>
                        <input type="text" name="page_url" class="form-control @error('page_url') is-invalid @enderror"
                            value="{{ old('page_url', $page->page_url) }}" required>
                        @error('page_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <hr class="my-4">
            @endif

            <x-admin.seo-fields
                :full="true"
                :seo-title="old('seo_title', $page->seo_title)"
                :meta-description="old('meta_description', $page->meta_description)"
                :focus-keyword="old('focus_keyword', $page->focus_keyword)"
                :canonical-url="old('canonical_url', $page->canonical_url)"
                :og-title="old('og_title', $page->og_title)"
                :og-description="old('og_description', $page->og_description)"
                :og-image="$page->og_image" />

            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <x-admin.select name="robots_meta" label="Robots Meta"
                        :options="['index, follow' => 'Index, Follow', 'noindex, follow' => 'No-Index, Follow', 'index, nofollow' => 'Index, No-Follow', 'noindex, nofollow' => 'No-Index, No-Follow']"
                        :selected="old('robots_meta', $page->robots_meta)" />
                </div>
                <div class="col-md-4">
                    <x-admin.input name="twitter_title" label="Twitter Title" :value="old('twitter_title', $page->twitter_title)" />
                </div>
                <div class="col-md-4">
                    <x-admin.image-upload name="twitter_image" label="Twitter Image" :existing="$page->twitter_image" />
                </div>
                <div class="col-12">
                    <label class="form-label small fw-medium">Twitter Description</label>
                    <textarea name="twitter_description" class="form-control" rows="2" maxlength="500">{{ old('twitter_description', $page->twitter_description) }}</textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2 align-items-center">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save</button>
                <a href="{{ route('admin.seo.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>

                @can('seo.delete')
                    @unless ($page->is_default)
                        <form action="{{ route('admin.seo.destroy', $page->id) }}" method="POST"
                              class="ms-auto"
                              onsubmit="return confirm('Delete SEO settings for &quot;{{ $page->page_label }}&quot;? This can\'t be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i> Delete Page</button>
                        </form>
                    @endunless
                @endcan
            </div>
        </form>
    </div>
</div>
@endsection
