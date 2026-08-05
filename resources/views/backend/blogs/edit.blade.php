@extends('backend.layouts.master')

@section('title', 'Edit Blog')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Blogs</a></li>
            <li class="breadcrumb-item active">Edit — {{ $blog->title }}</li>
        </ol>
    </nav>
    <h4>Edit Blog</h4>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('backend.blogs.form')
        </form>
    </div>
</div>
@endsection
