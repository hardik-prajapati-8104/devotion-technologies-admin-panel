@extends('backend.layouts.master')

@section('title', 'SEO Management')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">SEO Management</li>
        </ol>
    </nav>
    <h4>SEO Management</h4>
    <p class="subtitle">Meta tags for pages that aren't tied to a single content record, like Home and About Us. Services, Projects, Blogs, and Careers each manage their own SEO fields on their edit forms.</p>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>SEO Title</th>
                    <th>Meta Description</th>
                    <th width="8%">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pages as $page)
                <tr>
                    <td class="fw-medium">{{ $page->page_label }}</td>
                    <td>{{ $page->seo_title ?: '—' }}</td>
                    <td class="text-truncate" style="max-width: 320px;">{{ $page->meta_description ?: '—' }}</td>
                    <td>
                        @can('seo.edit')
                            <a href="{{ route('admin.seo.edit', $page->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
