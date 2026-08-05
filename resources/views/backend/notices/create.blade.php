@extends('backend.layouts.master')

@section('title', 'New Notice')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.notices.index') }}">Notices</a></li>
            <li class="breadcrumb-item active">New Notice</li>
        </ol>
    </nav>
    <div>
        <h4>New Notice</h4>
        <p class="subtitle">Target this notice at specific roles, or leave the audience empty to notify everyone.</p>
    </div>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.notices.store') }}" method="POST">
            @include('backend.notices.form')
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.notices.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Save Notice</button>
            </div>
        </form>
    </div>
</div>
@endsection
