@extends('backend.layouts.master')

@section('title', 'Edit Home Banner')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.home-banners.index') }}">Home Banners</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Edit Home Banner</h4>
            <p class="subtitle">Update "{{ $banner->title ?: 'this banner' }}".</p>
        </div>
        {{-- @can('home-banners.delete') --}}
            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('delete-banner-{{ $banner->id }}', '{{ $banner->title ?: 'this banner' }}')">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
            <form id="delete-banner-{{ $banner->id }}" action="{{ route('admin.home-banners.destroy', $banner->id) }}" method="POST" class="d-none">
                @csrf @method('DELETE')
            </form>
        {{-- @endcan --}}
    </div>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.home-banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @include('backend.home-banners.form')
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.home-banners.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Update Banner</button>
            </div>
        </form>
    </div>
</div>
@endsection
