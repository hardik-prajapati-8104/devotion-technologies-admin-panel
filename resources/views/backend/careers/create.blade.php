@extends('backend.layouts.master')

@section('title', 'Add Job Listing')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.careers.index') }}">Careers</a></li>
            <li class="breadcrumb-item active">Add Job Listing</li>
        </ol>
    </nav>
    <h4>Add Job Listing</h4>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.careers.store') }}" method="POST">
            @csrf
            @include('backend.careers.form')
        </form>
    </div>
</div>
@endsection
