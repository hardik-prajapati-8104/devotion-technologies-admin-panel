@extends('backend.layouts.master')

@section('title', 'Edit Blog Category')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blog-categories.index') }}">Categories</a></li>
            <li class="breadcrumb-item active">Edit — {{ $category->name }}</li>
        </ol>
    </nav>
    <h4>Edit Blog Category</h4>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.blog-categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('backend.blog-categories.form')
        </form>
    </div>
</div>
@endsection
