@extends('backend.layouts.master')

@section('title', 'Add SEO Page')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.seo.index') }}">SEO Management</a></li>
            <li class="breadcrumb-item active">Add New Page</li>
        </ol>
    </nav>
    <h4>Add New SEO Page</h4>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.seo.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label small fw-medium">Page Label <span class="text-danger">*</span></label>
                    <input type="text" name="page_label" class="form-control @error('page_label') is-invalid @enderror"
                        value="{{ old('page_label') }}" placeholder="e.g. Privacy Policy Page" required>
                    <div class="form-text">A friendly name shown in the SEO list. Not shown on the front-end.</div>
                    @error('page_label') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-medium">Page URL <span class="text-danger">*</span></label>
                    <input type="text" name="page_url" class="form-control @error('page_url') is-invalid @enderror"
                        value="{{ old('page_url') }}" placeholder="e.g. /privacy-policy" required>
                    <div class="form-text">The front-end path this SEO data applies to. Paste a full URL or just the path — only the path is stored.</div>
                    @error('page_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <hr class="my-4">

            <x-admin.seo-fields
                :full="true"
                :seo-title="old('seo_title')"
                :meta-description="old('meta_description')"
                :focus-keyword="old('focus_keyword')"
                :canonical-url="old('canonical_url')"
                :og-title="old('og_title')"
                :og-description="old('og_description')"
                :og-image="null" />

            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <x-admin.select name="robots_meta" label="Robots Meta"
                        :options="['index, follow' => 'Index, Follow', 'noindex, follow' => 'No-Index, Follow', 'index, nofollow' => 'Index, No-Follow', 'noindex, nofollow' => 'No-Index, No-Follow']"
                        :selected="old('robots_meta', 'index, follow')" />
                </div>
                <div class="col-md-4">
                    <x-admin.input name="twitter_title" label="Twitter Title" :value="old('twitter_title')" />
                </div>
                <div class="col-md-4">
                    <x-admin.image-upload name="twitter_image" label="Twitter Image" />
                </div>
                <div class="col-12">
                    <label class="form-label small fw-medium">Twitter Description</label>
                    <textarea name="twitter_description" class="form-control" rows="2" maxlength="500">{{ old('twitter_description') }}</textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Add Page</button>
                <a href="{{ route('admin.seo.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
            </div>
        </form>
    </div>
</div>
@endsection
