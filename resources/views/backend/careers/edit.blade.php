@extends('backend.layouts.master')

@section('title', 'Edit Job Listing')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.careers.index') }}">Careers</a></li>
            <li class="breadcrumb-item active">Edit — {{ $career->title }}</li>
        </ol>
    </nav>
    <h4>Edit Job Listing</h4>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.careers.update', $career->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('backend.careers._form')
        </form>
    </div>
</div>
@endsection
