<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();

        // Each stat is guarded with Schema::hasTable() so the dashboard
        // still renders in Phase 1, before the module tables from Phase
        // 3-6 (projects, blogs, services, etc.) exist. Replace the
        // Schema checks with direct Model::count() calls as each module
        // is built.
        $stats = [
            'projects'      => $this->safeCount('projects'),
            'services'      => $this->safeCount('services'),
            'blogs_published' => $this->safeCount('blogs', ['status' => 'published']),
            'blogs_draft'   => $this->safeCount('blogs', ['status' => 'draft']),
            'testimonials'  => $this->safeCount('testimonials'),
            'faqs'          => $this->safeCount('faqs'),
            'careers'       => $this->safeCount('careers'),
            'enquiries'     => $this->safeCount('contact_enquiries'),
            'subscribers'   => $this->safeCount('newsletter_subscribers'),
            'team_members'  => $this->safeCount('team_members'),
        ];

        // Monthly enquiries for the Chart.js line chart (last 6 months).
        $enquiryChart = $this->monthlySeries('contact_enquiries');

        $recentActivity = Schema::hasTable('activity_logs')
            ? ActivityLog::with('admin')->latest()->limit(8)->get()
            : collect();

        return view('backend.dashboard.index', compact('admin', 'stats', 'enquiryChart', 'recentActivity'));
    }

    private function safeCount(string $table, array $where = []): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        $query = \Illuminate\Support\Facades\DB::table($table)->whereNull('deleted_at');

        foreach ($where as $col => $val) {
            $query->where($col, $val);
        }

        return $query->count();
    }

    private function monthlySeries(string $table): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M Y');

            $data[] = Schema::hasTable($table)
                ? \Illuminate\Support\Facades\DB::table($table)
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count()
                : 0;
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
