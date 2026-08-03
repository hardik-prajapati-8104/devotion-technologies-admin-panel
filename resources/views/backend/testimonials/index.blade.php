@extends('backend.layouts.master')

@section('title', 'Testimonials')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Testimonials</li>
        </ol>
    </nav>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4>Testimonials</h4>
            <p class="subtitle">Client reviews shown across the website. Drag rows to reorder.</p>
        </div>
        @can('testimonials.create')
            <a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Testimonial
            </a>
        @endcan
    </div>
@endsection

@section('admin-content')
<div class="card">
    <div class="card-body">
        @if ($testimonials->isEmpty())
            <x-admin.empty-state
                icon="bi-chat-quote"
                title="No testimonials yet"
                description="Add your first client testimonial."
                actionLabel="Add Testimonial"
                :actionUrl="route('admin.testimonials.create')" />
        @else
            <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle" id="sortable-table">
                <thead>
                    <tr>
                        <th width="2%"></th>
                        <th>Client</th>
                        <th>Rating</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th width="8%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($testimonials as $testimonial)
                    <tr data-id="{{ $testimonial->id }}">
                        <td class="text-center text-muted" style="cursor:grab;"><i class="bi bi-grip-vertical"></i></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $testimonial->profile_image ? asset('storage/'.$testimonial->profile_image) : 'https://ui-avatars.com/api/?background=aa8038&color=fff&name='.urlencode($testimonial->client_name) }}"
                                     width="32" height="32" class="rounded-circle" style="object-fit:cover;">
                                <div>
                                    {{ $testimonial->client_name }}
                                    @if ($testimonial->company)
                                        <div class="text-muted" style="font-size:11.5px;">{{ $testimonial->designation }} @if($testimonial->designation && $testimonial->company) &middot; @endif {{ $testimonial->company }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= $testimonial->rating ? '-fill text-warning' : ' text-muted' }}"></i>
                            @endfor
                        </td>
                        <td>{!! $testimonial->is_featured ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-muted"></i>' !!}</td>
                        <td><x-admin.status-badge :status="$testimonial->status" /></td>
                        <td>
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">&#x22EE;</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @can('testimonials.edit')
                                    <li><a class="dropdown-item" href="{{ route('admin.testimonials.edit', $testimonial->id) }}"><i class="bi bi-pencil me-2"></i>Edit</a></li>
                                @endcan
                                @can('testimonials.delete')
                                    <li>
                                        <x-admin.confirm-delete
                                            :action="route('admin.testimonials.destroy', $testimonial->id)"
                                            :form-id="'delete-testimonial-'.$testimonial->id"
                                            :label="$testimonial->client_name" />
                                    </li>
                                @endcan
                            </ul>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="mt-3">{{ $testimonials->links() }}</div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    const sortableBody = document.querySelector('#sortable-table tbody');
    if (sortableBody) {
        Sortable.create(sortableBody, {
            handle: '.bi-grip-vertical',
            animation: 150,
            onEnd: function () {
                const order = Array.from(sortableBody.querySelectorAll('tr')).map(row => row.dataset.id);
                fetch('{{ route('admin.testimonials.reorder') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ order }),
                });
            }
        });
    }
</script>
@endsection
