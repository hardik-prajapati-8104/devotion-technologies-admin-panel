@extends('backend.layouts.master')

@section('title', 'Add Role')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles &amp; Permissions</a></li>
            <li class="breadcrumb-item active">Add Role</li>
        </ol>
    </nav>
    <h4>Add Role</h4>
    <p class="subtitle">Define a new role and choose which permissions it grants.</p>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            @include('backend.roles.form')
        </form>
    </div>
</div>
@endsection
