<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminRolesSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard.view',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'services.view', 'services.create', 'services.edit', 'services.delete',
            'projects.view', 'projects.create', 'projects.edit', 'projects.delete',
            'blogs.view', 'blogs.create', 'blogs.edit', 'blogs.delete', 'blogs.publish',
            'testimonials.view', 'testimonials.create', 'testimonials.edit', 'testimonials.delete',
            'faqs.view', 'faqs.create', 'faqs.edit', 'faqs.delete',
            'team.view', 'team.create', 'team.edit', 'team.delete',
            'careers.view', 'careers.create', 'careers.edit', 'careers.delete',
            'applications.view', 'applications.update', 'applications.delete',
            'enquiries.view', 'enquiries.update', 'enquiries.delete',
            'subscribers.view', 'subscribers.edit', 'subscribers.delete',
            'media.view', 'media.upload', 'media.delete',
            'seo.view', 'seo.edit',
            'settings.view', 'settings.edit',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'admin.view', 'admin.create', 'admin.edit', 'admin.delete',
            'activity-logs.view',
            'menus.view', 'menus.create', 'menus.edit', 'menus.delete',
            'countries.view', 'countries.create', 'countries.edit', 'countries.delete',
            // Communication module
            'chat.view',
            'messages.view',
            'announcements.view', 'announcements.create', 'announcements.edit', 'announcements.delete',
            'notices.view', 'notices.create', 'notices.edit', 'notices.delete',
            'support-tickets.view', 'support-tickets.create', 'support-tickets.edit', 'support-tickets.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'admin']);
        $superAdmin->syncPermissions($permissions);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        $admin->syncPermissions(collect($permissions)->reject(fn ($p) => str_starts_with($p, 'admin.') || str_starts_with($p, 'roles.'))->all());

        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'admin']);
        $editor->syncPermissions([
            'dashboard.view',
            'blogs.view', 'blogs.create', 'blogs.edit',
            'services.view', 'services.create', 'services.edit',
            'projects.view', 'projects.create', 'projects.edit',
            'testimonials.view', 'testimonials.create', 'testimonials.edit',
            'faqs.view', 'faqs.create', 'faqs.edit',
            'countries.view',
            // Every admin should be reachable for chat/messages and be able
            // to see announcements/notices/tickets aimed at them.
            'chat.view', 'messages.view',
            'announcements.view', 'notices.view',
            'support-tickets.view', 'support-tickets.create',
        ]);

        $contentManager = Role::firstOrCreate(['name' => 'content-manager', 'guard_name' => 'admin']);
        $contentManager->syncPermissions([
            'dashboard.view',
            'blogs.view', 'blogs.create', 'blogs.edit', 'blogs.delete', 'blogs.publish',
            'media.view', 'media.upload', 'media.delete',
            'seo.view', 'seo.edit',
            'chat.view', 'messages.view',
            'announcements.view', 'notices.view',
            'support-tickets.view', 'support-tickets.create',
        ]);

        // Default Super Admin login (change the password immediately after seeding).
        // Credentials are pulled from .env so nothing sensitive is hardcoded.
        $superAdminEmail = env('SUPER_ADMIN_EMAIL', 'superadmin@devotiontechnology.com');
        $superAdminPassword = env('SUPER_ADMIN_PASSWORD', 'ChangeMe@123');

        $account = Admin::firstOrCreate(
            ['email' => $superAdminEmail],
            [
                'first_name' => 'Super',
                'last_name'  => 'Admin',
                'username'   => 'superadmin',
                'password'   => Hash::make($superAdminPassword),
                'status'     => 1,
                'login'      => 1,
            ]
        );

        if (! $account->hasRole('superadmin')) {
            $account->assignRole('superadmin');
        }
    }
}