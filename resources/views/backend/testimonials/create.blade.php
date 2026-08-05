@extends('backend.layouts.master')

@section('title', 'Add Testimonial')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.testimonials.index') }}">Testimonials</a></li>
            <li class="breadcrumb-item active">Add Testimonial</li>
        </ol>
    </nav>
    <h4>Add Testimonial</h4>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('backend.testimonials.form')
        </form>
    </div>
</div>
@endsection
