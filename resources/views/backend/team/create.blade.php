@extends('backend.layouts.master')

@section('title', 'Add Team Member')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.team.index') }}">Team</a></li>
            <li class="breadcrumb-item active">Add Team Member</li>
        </ol>
    </nav>
    <h4>Add Team Member</h4>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.team.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('backend.team._form')
        </form>
    </div>
</div>
@endsection
