@extends('backend.layouts.master')

@section('title', 'Contact Enquiries')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Contact Enquiries</li>
        </ol>
    </nav>
    <h4>Contact Enquiries</h4>
    <p class="subtitle">Messages submitted through the website's contact form.</p>
@endsection

@section('admin-content')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-medium">Search</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by name, email, or subject...">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-medium">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach (\App\Models\ContactEnquiry::STATUSES as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if ($enquiries->isEmpty())
            <x-admin.empty-state icon="bi-envelope" title="No enquiries found" description="Messages submitted through the contact form will appear here." />
        @else
        <form id="bulk-form" method="POST" action="{{ route('admin.enquiries.bulk-delete') }}">
            @csrf
            @method('DELETE')

            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="selectAll">
                    <label class="form-check-label small" for="selectAll">Select all</label>
                </div>
                @can('enquiries.delete')
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="bulkDeleteConfirm()">
                        <i class="bi bi-trash me-1"></i> Delete Selected
                    </button>
                @endcan
            </div>

            <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th width="2%"></th>
                        <th width="3%">#</th>
                        <th>From</th>
                        <th>Subject</th>
                        <th>Received</th>
                        <th>Status</th>
                        <th width="8%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($enquiries as $enquiry)
                    @php
                        $statusColors = ['new' => 'primary', 'read' => 'secondary', 'in_progress' => 'warning', 'replied' => 'success', 'closed' => 'dark'];
                    @endphp
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="{{ $enquiry->id }}" class="form-check-input row-checkbox"></td>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>
                            {{ $enquiry->name }}
                            <div class="text-muted" style="font-size:11.5px;">{{ $enquiry->email }}</div>
                        </td>
                        <td>{{ $enquiry->subject ?? '—' }}</td>
                        <td>{{ $enquiry->created_at->diffForHumans() }}</td>
                        <td><span class="badge bg-{{ $statusColors[$enquiry->status] ?? 'secondary' }} text-capitalize">{{ $enquiry->status_label }}</span></td>
                        <td>
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">&#x22EE;</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('admin.enquiries.show', $enquiry->id) }}"><i class="bi bi-eye me-2"></i>View</a></li>
                                @can('enquiries.delete')
                                    <li>
                                        <x-admin.confirm-delete
                                            :action="route('admin.enquiries.destroy', $enquiry->id)"
                                            :form-id="'delete-enquiry-'.$enquiry->id"
                                            :label="'the enquiry from '.$enquiry->name" />
                                    </li>
                                @endcan
                            </ul>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </form>

        <div class="mt-3">{{ $enquiries->links() }}</div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('selectAll')?.addEventListener('change', function () {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
    });

    function bulkDeleteConfirm() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        if (checked.length === 0) {
            Swal.fire('No enquiries selected', 'Please select at least one enquiry to delete.', 'info');
            return;
        }
        Swal.fire({
            title: 'Are you sure?',
            text: `This will permanently remove ${checked.length} enquiry(ies). This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#aa8038',
            confirmButtonText: 'Yes, delete them'
        }).then((result) => {
            if (result.isConfirmed) document.getElementById('bulk-form').submit();
        });
    }
</script>
@endsection
