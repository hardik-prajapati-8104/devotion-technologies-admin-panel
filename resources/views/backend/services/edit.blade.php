@extends('backend.layouts.master')

@section('title', 'Edit Service')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.services.index') }}">Services</a></li>
            <li class="breadcrumb-item active">Edit — {{ $service->name }}</li>
        </ol>
    </nav>
    <h4>Edit Service</h4>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('backend.services.form')
        </form>
    </div>
</div>
@endsection
