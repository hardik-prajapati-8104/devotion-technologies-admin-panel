@extends('backend.layouts.master')

@section('title', 'Add Blog')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Blogs</a></li>
            <li class="breadcrumb-item active">Add Blog</li>
        </ol>
    </nav>
    <h4>Add Blog</h4>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('backend.blogs._form')
        </form>
    </div>
</div>
@endsection
