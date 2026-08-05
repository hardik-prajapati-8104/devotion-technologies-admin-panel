@extends('backend.layouts.master')

@section('title', 'Edit Project')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
            <li class="breadcrumb-item active">Edit — {{ $project->name }}</li>
        </ol>
    </nav>
    <h4>Edit Project</h4>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.projects.update', $project->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('backend.projects.form')
        </form>
    </div>
</div>
@endsection
