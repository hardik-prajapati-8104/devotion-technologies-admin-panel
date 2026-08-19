@extends('backend.layouts.master')

@section('title', 'Users')

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endsection

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Users</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4>Users</h4>
            <p class="subtitle">Manage admin panel accounts and their assigned roles.</p>
        </div>
        @can('users.create')
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add User
            </a>
        @endcan
    </div>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-striped align-middle w-100">
            <thead>
                <tr>
                    <th width="3%">#</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th width="8%">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $data)
                <tr>
                    <td class="text-center">{{ $loop->index + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $data->profile_image ? asset('storage/app/public/' . $data->profile_image) : 'https://ui-avatars.com/api/?background=aa8038&color=fff&name='.urlencode($data->name) }}"
                                 class="rounded-circle" width="32" height="32" style="object-fit:cover;">
                            {{ $data->name }}
                        </div>
                    </td>
                    <td>{{ $data->username }}</td>
                    <td>{{ $data->email }}</td>
                    <td>
                        @foreach ($data->roles as $role)
                            <span class="badge bg-secondary text-capitalize">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        <span class="status-badge {{ $data->status ? 'active' : 'inactive' }}">
                            {{ $data->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            &#x22EE;
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @can('users.edit')
                                <li><a class="dropdown-item" href="{{ route('admin.users.edit', $data->id) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                            @endcan
                            @can('users.delete')
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); confirmDelete('delete-form-{{ $data->id }}', '{{ $data->name }}');">
                                        <i class="bi bi-trash me-2"></i>Delete
                                    </a>
                                    <form id="delete-form-{{ $data->id }}" action="{{ route('admin.users.destroy', $data->id) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </li>
                            @endcan
                        </ul>
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
                order: [[1, 'asc']]
            });
        });
    </script>
@endsection
