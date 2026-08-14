@extends('backend.layouts.master')

@section('title', 'Edit Client')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Our Clients</a></li>
            <li class="breadcrumb-item active">Edit Client</li>
        </ol>
    </nav>
    <div>
        <h4>Edit Client — {{ $client->client_name }}</h4>
        <p class="subtitle">Update this client's details shown across the panel and public site.</p>
    </div>
@endsection

@section('admin-content')

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.clients.update', $client->id) }}" method="POST" enctype="multipart/form-data">
            @include('backend.clients.form')

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2 me-1"></i> Update Client
                </button>
            </div>
        </form>
    </div>
</div>

@endsection