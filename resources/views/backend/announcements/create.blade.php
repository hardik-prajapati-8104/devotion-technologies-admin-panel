@extends('backend.layouts.master')

@section('title', 'New Announcement')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.announcements.index') }}">Announcements</a></li>
            <li class="breadcrumb-item active">New Announcement</li>
        </ol>
    </nav>
    <div>
        <h4>New Announcement</h4>
        <p class="subtitle">This will be visible to everyone in the panel once published.</p>
    </div>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.announcements.store') }}" method="POST">
            @include('backend.announcements.form')
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Save Announcement</button>
            </div>
        </form>
    </div>
</div>
@endsection
