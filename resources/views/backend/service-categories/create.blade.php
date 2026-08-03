@extends('backend.layouts.master')

@section('title', 'Add Service Category')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.service-categories.index') }}">Categories</a></li>
            <li class="breadcrumb-item active">Add Category</li>
        </ol>
    </nav>
    <h4>Add Service Category</h4>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.service-categories.store') }}" method="POST">
            @csrf
            @include('backend.service-categories._form')
        </form>
    </div>
</div>
@endsection
