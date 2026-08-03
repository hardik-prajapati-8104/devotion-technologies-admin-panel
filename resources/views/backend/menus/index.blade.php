@extends('backend.layouts.master')

@section('title', 'Admin Menu Management')

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endsection

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Admin Menu Management</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
        <div>
            <h4 class="mb-0">Admin Menu Management</h4>
            <p class="subtitle mb-0">Manage the dynamic sidebar hierarchy and permissions for the admin panel.</p>
        </div>
        @can('menus.create')
            <a href="{{ route('admin.menus.create') }}" class="btn btn-sm text-white" style="background:#aa8038;">
                <i class="bi bi-plus-lg"></i> Add Menu Item
            </a>
        @endcan
    </div>
@endsection

@section('admin-content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped align-middle w-100">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="12%">Parent</th>
                            <th width="18%">Name</th>
                            <th width="18%">Route</th>
                            <th width="10%">Icon</th>
                            <th width="10%">Status</th>
                            <th width="8%">Sort</th>
                            <th width="14%">Created</th>
                            <th width="14%">Updated</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($menus as $menu)
                            <tr data-id="{{ $menu->id }}">
                                <td class="text-center">{{ $menu->id }}</td>
                                <td>{{ $menu->parent?->title ?? '—' }}</td>
                                <td>
                                    @if ($menu->icon)
                                        <i class="{{ $menu->icon }} me-1"></i>
                                    @endif
                                    <span class="{{ $menu->parent_id ? 'ps-2' : '' }}">{{ $menu->title }}</span>
                                </td>
                                <td><code>{{ $menu->route_name ?? '—' }}</code></td>
                                <td>{{ $menu->icon ?? '—' }}</td>
                                <td>
                                    <span class="status-badge {{ $menu->is_active ? 'active' : 'inactive' }}">
                                        {{ $menu->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $menu->sort_order }}</td>
                                <td>{{ $menu->created_at?->format('Y-m-d H:i:s') ?? '—' }}</td>
                                <td>{{ $menu->updated_at?->format('Y-m-d H:i:s') ?? '—' }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @can('menus.edit')
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.menus.edit', $menu) }}">
                                                        <i class="bi bi-pencil me-2"></i>Edit
                                                    </a>
                                                </li>
                                            @endcan
                                            @can('menus.delete')
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); confirmDelete('delete-menu-{{ $menu->id }}', '{{ $menu->title }}');">
                                                        <i class="bi bi-trash me-2"></i>Delete
                                                    </a>
                                                    <form id="delete-menu-{{ $menu->id }}" action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="d-none">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script>
        $(function () {
            $('#dataTable').DataTable({
                responsive: true,
                order: [[0, 'asc']]
            });
        });
    </script>
@endsection
