@extends('backend.layouts.master')

@section('title', 'Edit Role')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles &amp; Permissions</a></li>
            <li class="breadcrumb-item active">Edit — {{ ucfirst($role->name) }}</li>
        </ol>
    </nav>
    <h4>Edit Role</h4>
    <p class="subtitle">Update the permissions granted to this role.</p>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('backend.roles._form')
        </form>
    </div>
</div>
@endsection
