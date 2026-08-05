@extends('backend.layouts.master')

@section('title', 'Edit Notice')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.notices.index') }}">Notices</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Edit Notice</h4>
            <p class="subtitle">Update "{{ $notice->title }}".</p>
        </div>
        @can('notices.delete')
            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('delete-notice-{{ $notice->id }}', '{{ $notice->title }}')">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
            <form id="delete-notice-{{ $notice->id }}" action="{{ route('admin.notices.destroy', $notice->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endcan
    </div>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.notices.update', $notice->id) }}" method="POST">
            @method('PUT')
            @include('backend.notices.form')
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.notices.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Update Notice</button>
            </div>
        </form>
    </div>
</div>
@endsection
