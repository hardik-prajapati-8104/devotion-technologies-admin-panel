@extends('backend.layouts.master')

@section('title', 'Add FAQ')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.faqs.index') }}">FAQs</a></li>
            <li class="breadcrumb-item active">Add FAQ</li>
        </ol>
    </nav>
    <h4>Add FAQ</h4>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.faqs.store') }}" method="POST">
            @csrf
            @include('backend.faqs.form')
        </form>
    </div>
</div>
@endsection
