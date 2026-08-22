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
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.seo.update', $page->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-admin.seo-fields
                :full="true"
                :seo-title="$page->seo_title"
                :meta-description="$page->meta_description"
                :focus-keyword="$page->focus_keyword"
                :canonical-url="$page->canonical_url"
                :og-title="$page->og_title"
                :og-description="$page->og_description"
                :og-image="$page->og_image" />

            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <x-admin.select name="robots_meta" label="Robots Meta"
                        :options="['index, follow' => 'Index, Follow', 'noindex, follow' => 'No-Index, Follow', 'index, nofollow' => 'Index, No-Follow', 'noindex, nofollow' => 'No-Index, No-Follow']"
                        :selected="$page->robots_meta" />
                </div>
                <div class="col-md-4">
                    <x-admin.input name="twitter_title" label="Twitter Title" :value="$page->twitter_title" />
                </div>
                <div class="col-md-4">
                    <x-admin.image-upload name="twitter_image" label="Twitter Image" :existing="$page->twitter_image" />
                </div>
                <div class="col-12">
                    <label class="form-label small fw-medium">Twitter Description</label>
                    <textarea name="twitter_description" class="form-control" rows="2" maxlength="500">{{ old('twitter_description', $page->twitter_description) }}</textarea>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save</button>
                <a href="{{ route('admin.seo.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
            </div>
        </form>
    </div>
</div>
@endsection
