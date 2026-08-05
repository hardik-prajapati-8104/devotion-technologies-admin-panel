@extends('backend.layouts.master')

@section('title', 'Add Country')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.countries.index') }}">Countries</a></li>
            <li class="breadcrumb-item active">Add Country</li>
        </ol>
    </nav>
    <div>
        <h4>Add Country</h4>
        <p class="subtitle">Create a new country entry for use across the panel and public site.</p>
    </div>
@endsection

@section('admin-content')

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.countries.store') }}" method="POST" enctype="multipart/form-data">
            @include('backend.countries.form')

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.countries.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2 me-1"></i> Save Country
                </button>
            </div>
        </form>
    </div>
</div>

@endsection