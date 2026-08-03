@extends('backend.layouts.master')

@section('title', 'Dashboard')

@section('page-header')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
    <h4>Welcome back, {{ $admin->name ?? 'Admin' }} 👋</h4>
    <p class="subtitle">Here's what's happening with Devotion Technology today.</p>
@endsection

@section('admin-content')

    <!-- Stat cards -->
    <div class="row g-3 mb-4">
        @php
            $cards = [
                ['icon' => 'bi-kanban-fill', 'label' => 'Total Projects', 'value' => $stats['projects']],
                ['icon' => 'bi-briefcase-fill', 'label' => 'Total Services', 'value' => $stats['services']],
                ['icon' => 'bi-journal-check', 'label' => 'Published Blogs', 'value' => $stats['blogs_published']],
                ['icon' => 'bi-journal-x', 'label' => 'Draft Blogs', 'value' => $stats['blogs_draft']],
                ['icon' => 'bi-chat-quote-fill', 'label' => 'Testimonials', 'value' => $stats['testimonials']],
                ['icon' => 'bi-patch-question-fill', 'label' => 'FAQs', 'value' => $stats['faqs']],
                ['icon' => 'bi-briefcase', 'label' => 'Career Openings', 'value' => $stats['careers']],
                ['icon' => 'bi-envelope-fill', 'label' => 'Contact Enquiries', 'value' => $stats['enquiries']],
                ['icon' => 'bi-newspaper', 'label' => 'Newsletter Subscribers', 'value' => $stats['subscribers']],
                ['icon' => 'bi-people-fill', 'label' => 'Team Members', 'value' => $stats['team_members']],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="col-6 col-md-4 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="bi {{ $card['icon'] }}"></i></div>
                    <div>
                        <div class="stat-value">{{ number_format($card['value']) }}</div>
                        <div class="stat-label">{{ $card['label'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Charts -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Enquiries Overview <span class="text-muted fw-normal small">(last 6 months)</span></h6>
                    <canvas id="enquiryChart" height="110"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Blog Status</h6>
                    <canvas id="blogChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent activity -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Recent Activity</h6>

                    @forelse ($recentActivity as $log)
                        <div class="d-flex align-items-start gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="stat-icon" style="width:36px;height:36px;font-size:14px;">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div>
                                <div class="small">
                                    <strong>{{ $log->admin->name ?? 'System' }}</strong>
                                    {{ $log->description }}
                                </div>
                                <div class="text-muted" style="font-size:11.5px;">{{ $log->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">No activity recorded yet. Once modules from later phases are built, admin actions will appear here.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const enquiryCtx = document.getElementById('enquiryChart');
    new Chart(enquiryCtx, {
        type: 'line',
        data: {
            labels: @json($enquiryChart['labels']),
            datasets: [{
                label: 'Enquiries',
                data: @json($enquiryChart['data']),
                borderColor: '#aa8038',
                backgroundColor: 'rgba(170,128,56,0.12)',
                tension: 0.35,
                fill: true,
                pointRadius: 3
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    const blogCtx = document.getElementById('blogChart');
    new Chart(blogCtx, {
        type: 'doughnut',
        data: {
            labels: ['Published', 'Draft'],
            datasets: [{
                data: [{{ $stats['blogs_published'] }}, {{ $stats['blogs_draft'] }}],
                backgroundColor: ['#aa8038', '#e9ecef']
            }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endsection
