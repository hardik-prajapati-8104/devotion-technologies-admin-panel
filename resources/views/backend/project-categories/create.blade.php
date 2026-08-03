@extends('backend.layouts.master')

@section('title', 'Add Project Category')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.project-categories.index') }}">Categories</a></li>
            <li class="breadcrumb-item active">Add Category</li>
        </ol>
    </nav>
    <h4>Add Project Category</h4>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.project-categories.store') }}" method="POST">
            @csrf
            @include('backend.project-categories._form')
        </form>
    </div>
</div>
@endsection
