@extends('backend.layouts.master')

@section('title', 'Roles & Permissions')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Roles &amp; Permissions</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4>Roles &amp; Permissions</h4>
            <p class="subtitle">Control what each role is allowed to do across the admin panel.</p>
        </div>
        @can('roles.create')
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Role
            </a>
        @endcan
    </div>
@endsection

@section('admin-content')
<div class="row g-3">
    @foreach ($roles ?? [] as $role)
        @php
            $roleName = data_get($role, 'name');
            $roleId = data_get($role, 'id');
            $permissionsCount = data_get($role, 'permissions_count', 0);
            $usersCount = data_get($role, 'users_count', 0);
        @endphp

        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="fw-semibold text-capitalize mb-1">{{ $roleName }}</h6>
                            <p class="text-muted small mb-0">
                                {{ $permissionsCount }} permissions &middot; {{ $usersCount }} users
                            </p>
                        </div>
                        @if ($roleName !== 'superadmin' && $roleId)
                            <div class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">&#x22EE;</button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @can('roles.edit')
                                        <li><a class="dropdown-item" href="{{ route('admin.roles.edit', ['role' => $roleId]) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                    @endcan
                                    @can('roles.delete')
                                        <li>
                                            <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); confirmDelete('delete-role-{{ $roleId }}', '{{ $roleName }} role');">
                                                <i class="bi bi-trash me-2"></i>Delete
                                            </a>
                                            <form id="delete-role-{{ $roleId }}" action="{{ route('admin.roles.destroy', ['role' => $roleId]) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        @elseif ($roleName === 'superadmin')
                            <span class="badge bg-secondary">System</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
