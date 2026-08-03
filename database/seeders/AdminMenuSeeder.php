<?php

namespace Database\Seeders;

use App\Models\AdminMenu;
use Illuminate\Database\Seeder;

class AdminMenuSeeder extends Seeder
{
    public function run(): void
    {
        AdminMenu::query()->delete();

        $sort = 0;

        // Dashboard
        AdminMenu::create([
            'type' => 'item',
            'title' => 'Dashboard',
            'icon' => 'bi bi-grid-1x2-fill',
            'route_name' => 'admin.dashboard',
            'sort_order' => $sort++,
        ]);

        // --- Website Content ------------------------------------------------
        AdminMenu::create([
            'type' => 'section',
            'title' => 'Website Content',
            'sort_order' => $sort++,
        ]);

        $services = AdminMenu::create([
            'type' => 'item',
            'title' => 'Services',
            'icon' => 'bi bi-briefcase-fill',
            'permission' => 'services.view',
            'route_pattern' => 'admin.services*',
            'sort_order' => $sort++,
        ]);
        AdminMenu::create(['parent_id' => $services->id, 'type' => 'item', 'title' => 'All Services', 'route_name' => 'admin.services.index', 'route_pattern' => 'admin.services.*', 'sort_order' => 0]);
        AdminMenu::create(['parent_id' => $services->id, 'type' => 'item', 'title' => 'Categories', 'route_name' => 'admin.service-categories.index', 'route_pattern' => 'admin.service-categories.*', 'sort_order' => 1]);

        $projects = AdminMenu::create([
            'type' => 'item',
            'title' => 'Projects',
            'icon' => 'bi bi-kanban-fill',
            'permission' => 'projects.view',
            'route_pattern' => 'admin.projects*',
            'sort_order' => $sort++,
        ]);
        AdminMenu::create(['parent_id' => $projects->id, 'type' => 'item', 'title' => 'All Projects', 'route_name' => 'admin.projects.index', 'route_pattern' => 'admin.projects.*', 'sort_order' => 0]);
        AdminMenu::create(['parent_id' => $projects->id, 'type' => 'item', 'title' => 'Categories', 'route_name' => 'admin.project-categories.index', 'route_pattern' => 'admin.project-categories.*', 'sort_order' => 1]);

        $blogs = AdminMenu::create([
            'type' => 'item',
            'title' => 'Blog Management',
            'icon' => 'bi bi-journal-richtext',
            'permission' => 'blogs.view',
            'route_pattern' => 'admin.blogs*,admin.blog-categories*,admin.blog-tags*',
            'sort_order' => $sort++,
        ]);
        AdminMenu::create(['parent_id' => $blogs->id, 'type' => 'item', 'title' => 'All Blogs', 'route_name' => 'admin.blogs.index', 'route_pattern' => 'admin.blogs.*', 'sort_order' => 0]);
        AdminMenu::create(['parent_id' => $blogs->id, 'type' => 'item', 'title' => 'Categories', 'route_name' => 'admin.blog-categories.index', 'route_pattern' => 'admin.blog-categories.*', 'sort_order' => 1]);
        AdminMenu::create(['parent_id' => $blogs->id, 'type' => 'item', 'title' => 'Tags', 'route_name' => 'admin.blog-tags.index', 'route_pattern' => 'admin.blog-tags.*', 'sort_order' => 2]);

        AdminMenu::create([
            'type' => 'item',
            'title' => 'Testimonials',
            'icon' => 'bi bi-chat-quote-fill',
            'permission' => 'testimonials.view',
            'route_name' => 'admin.testimonials.index',
            'route_pattern' => 'admin.testimonials.*',
            'sort_order' => $sort++,
        ]);

        $faqs = AdminMenu::create([
            'type' => 'item',
            'title' => 'FAQs',
            'icon' => 'bi bi-patch-question-fill',
            'permission' => 'faqs.view',
            'route_pattern' => 'admin.faqs*,admin.faq-categories*',
            'sort_order' => $sort++,
        ]);
        AdminMenu::create(['parent_id' => $faqs->id, 'type' => 'item', 'title' => 'All FAQs', 'route_name' => 'admin.faqs.index', 'route_pattern' => 'admin.faqs.*', 'sort_order' => 0]);
        AdminMenu::create(['parent_id' => $faqs->id, 'type' => 'item', 'title' => 'Categories', 'route_name' => 'admin.faq-categories.index', 'route_pattern' => 'admin.faq-categories.*', 'sort_order' => 1]);

        AdminMenu::create([
            'type' => 'item',
            'title' => 'Team Management',
            'icon' => 'bi bi-people-fill',
            'permission' => 'team.view',
            'route_name' => 'admin.team.index',
            'route_pattern' => 'admin.team.*',
            'sort_order' => $sort++,
        ]);

        // --- Recruitment ------------------------------------------------
        AdminMenu::create(['type' => 'section', 'title' => 'Recruitment', 'sort_order' => $sort++]);

        AdminMenu::create([
            'type' => 'item',
            'title' => 'Careers',
            'icon' => 'bi bi-briefcase-fill',
            'permission' => 'careers.view',
            'route_name' => 'admin.careers.index',
            'route_pattern' => 'admin.careers.*',
            'sort_order' => $sort++,
        ]);

        AdminMenu::create([
            'type' => 'item',
            'title' => 'Applications',
            'icon' => 'bi bi-file-earmark-person-fill',
            'permission' => 'applications.view',
            'route_name' => 'admin.applications.index',
            'route_pattern' => 'admin.applications.*',
            'badge_key' => 'new_applications',
            'sort_order' => $sort++,
        ]);

        // --- Communication ------------------------------------------------
        AdminMenu::create(['type' => 'section', 'title' => 'Communication', 'sort_order' => $sort++]);

        AdminMenu::create([
            'type' => 'item',
            'title' => 'Contact Enquiries',
            'icon' => 'bi bi-envelope-fill',
            'permission' => 'enquiries.view',
            'route_name' => 'admin.enquiries.index',
            'route_pattern' => 'admin.enquiries.*',
            'badge_key' => 'new_enquiries',
            'sort_order' => $sort++,
        ]);

        AdminMenu::create([
            'type' => 'item',
            'title' => 'Newsletter Subscribers',
            'icon' => 'bi bi-newspaper',
            'permission' => 'subscribers.view',
            'route_name' => 'admin.subscribers.index',
            'route_pattern' => 'admin.subscribers.*',
            'sort_order' => $sort++,
        ]);

        // --- System ------------------------------------------------
        AdminMenu::create(['type' => 'section', 'title' => 'System', 'sort_order' => $sort++]);

        AdminMenu::create(['type' => 'item', 'title' => 'Media / Gallery', 'icon' => 'bi bi-images', 'permission' => 'media.view', 'route_name' => 'admin.media.index', 'route_pattern' => 'admin.media.*', 'sort_order' => $sort++]);
        AdminMenu::create(['type' => 'item', 'title' => 'SEO Management', 'icon' => 'bi bi-search', 'permission' => 'seo.view', 'route_name' => 'admin.seo.index', 'route_pattern' => 'admin.seo.*', 'sort_order' => $sort++]);
        AdminMenu::create(['type' => 'item', 'title' => 'Users', 'icon' => 'bi bi-person-badge-fill', 'permission' => 'users.view', 'route_name' => 'admin.users.index', 'route_pattern' => 'admin.users.*', 'sort_order' => $sort++]);
        AdminMenu::create(['type' => 'item', 'title' => 'Roles & Permissions', 'icon' => 'bi bi-shield-lock-fill', 'permission' => 'roles.view', 'route_name' => 'admin.roles.index', 'route_pattern' => 'admin.roles.*', 'sort_order' => $sort++]);
        AdminMenu::create(['type' => 'item', 'title' => 'Settings', 'icon' => 'bi bi-gear-fill', 'permission' => 'settings.view', 'route_name' => 'admin.settings.index', 'route_pattern' => 'admin.settings.*', 'sort_order' => $sort++]);
        AdminMenu::create(['type' => 'item', 'title' => 'Activity Logs', 'icon' => 'bi bi-clock-history', 'permission' => 'activity-logs.view', 'route_name' => 'admin.activity-logs.index', 'route_pattern' => 'admin.activity-logs.*', 'sort_order' => $sort++]);
        AdminMenu::create(['type' => 'item', 'title' => 'Menu Management', 'icon' => 'bi bi-list-ul', 'permission' => 'menus.view', 'route_name' => 'admin.menus.index', 'route_pattern' => 'admin.menus.*', 'sort_order' => $sort++]);

        // Profile (no permission needed — every logged-in admin sees it)
        AdminMenu::create([
            'type' => 'item',
            'title' => 'Profile',
            'icon' => 'bi bi-person-circle',
            'route_name' => 'admin.profile.edit',
            'route_pattern' => 'admin.profile.*',
            'sort_order' => $sort++,
        ]);

        // Note: Logout is intentionally NOT seeded here — it needs a POST form + CSRF
        // token, so it stays as a hardcoded block at the bottom of sidebar.blade.php.
    }
}