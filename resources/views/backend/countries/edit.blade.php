@extends('backend.layouts.master')

@section('title', 'Edit Country')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.countries.index') }}">Countries</a></li>
            <li class="breadcrumb-item active">Edit {{ $country->name }}</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Edit Country</h4>
            <p class="subtitle">Update details for {{ $country->name }}.</p>
        </div>
        @can('countries.delete')
            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('delete-country-{{ $country->id }}', '{{ $country->name }}')">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
            <form id="delete-country-{{ $country->id }}" action="{{ route('admin.countries.destroy', $country->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endcan
    </div>
@endsection

@section('admin-content')

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.countries.update', $country->id) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @include('backend.countries.form')

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.countries.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2 me-1"></i> Update Country
                </button>
            </div>
        </form>
    </div>
</div>

@endsection