@extends('backend.layouts.master')

@section('title', 'Add Home Banner')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.home-banners.index') }}">Home Banners</a></li>
            <li class="breadcrumb-item active">Add Banner</li>
        </ol>
    </nav>
    <div>
        <h4>Add Home Banner</h4>
        <p class="subtitle">This banner is appended to the end of the homepage slider — drag to reorder afterward.</p>
    </div>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.home-banners.store') }}" method="POST" enctype="multipart/form-data">
            @include('backend.home-banners.form')
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.home-banners.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Save Banner</button>
            </div>
        </form>
    </div>
</div>
@endsection
