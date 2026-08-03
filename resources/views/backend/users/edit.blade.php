@extends('backend.layouts.master')

@section('title', 'Edit User')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
            <li class="breadcrumb-item active">Edit — {{ $admin->name }}</li>
        </ol>
    </nav>
    <h4>Edit User</h4>
    <p class="subtitle">Update account details, photo, and role assignment.</p>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.users.update', $admin->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('backend.users._form')
        </form>
    </div>
</div>
@endsection
