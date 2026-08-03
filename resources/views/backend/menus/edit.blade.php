@extends('backend.layouts.master')

@section('title', 'Edit Menu Item')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.menus.index') }}">Admin Menu Management</a></li>
            <li class="breadcrumb-item active">Edit Menu Item</li>
        </ol>
    </nav>
    <h4 class="mb-0">Edit Menu Item</h4>
@endsection

@section('admin-content')

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.menus.update', $menu) }}" method="POST">
            @method('PUT')
            @include('backend.menus._form')
        </form>
    </div>
</div>
@endsection
