@extends('backend.layouts.master')

@section('title', 'Blog Tags')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Blogs</a></li>
            <li class="breadcrumb-item active">Tags</li>
        </ol>
    </nav>
    <h4>Blog Tags</h4>
    <p class="subtitle">Tags are created automatically when you type them into a blog's Tags field. Remove unused ones here.</p>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        @if ($tags->isEmpty())
            <x-admin.empty-state icon="bi-tags" title="No tags yet" description="Tags will appear here once you add them to a blog post." />
        @else
            <div class="d-flex flex-wrap gap-2">
                @foreach ($tags as $tag)
                    <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-2 py-2 px-3">
                        {{ $tag->name }}
                        <span class="text-muted">({{ $tag->blogs_count }})</span>
                        @can('blogs.delete')
                            <a href="#" class="text-danger" onclick="event.preventDefault(); confirmDelete('delete-tag-{{ $tag->id }}', '{{ $tag->name }}');">
                                <i class="bi bi-x-circle"></i>
                            </a>
                            <form id="delete-tag-{{ $tag->id }}" action="{{ route('admin.blog-tags.destroy', $tag->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endcan
                    </span>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
