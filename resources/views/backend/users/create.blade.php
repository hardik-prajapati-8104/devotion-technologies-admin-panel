@extends('backend.layouts.master')

@section('title', 'Add User')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">Add User</li>
        </ol>
    </nav>
    <h4>Add User</h4>
    <p class="subtitle">Create a new admin panel account and assign roles.</p>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('backend.users._form')
        </form>
    </div>
</div>
@endsection
