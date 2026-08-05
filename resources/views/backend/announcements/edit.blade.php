@extends('backend.layouts.master')

@section('title', 'Edit Announcement')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.announcements.index') }}">Announcements</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4>Edit Announcement</h4>
            <p class="subtitle">Update "{{ $announcement->title }}".</p>
        </div>
        @can('announcements.delete')
            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete('delete-announcement-{{ $announcement->id }}', '{{ $announcement->title }}')">
                <i class="bi bi-trash me-1"></i> Delete
            </button>
            <form id="delete-announcement-{{ $announcement->id }}" action="{{ route('admin.announcements.destroy', $announcement->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endcan
    </div>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST">
            @method('PUT')
            @include('backend.announcements.form')
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Update Announcement</button>
            </div>
        </form>
    </div>
</div>
@endsection
