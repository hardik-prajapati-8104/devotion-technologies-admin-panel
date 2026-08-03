<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\AdminMenu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AdminMenuSidebarIconsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_submenu_child_icon_is_rendered(): void
    {
        $admin = Admin::factory()->create();

        foreach (['menus.view'] as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        $admin->givePermissionTo(['menus.view']);

        $section = AdminMenu::create([
            'type' => 'section',
            'title' => 'System',
            'sort_order' => 0,
        ]);

        AdminMenu::create([
            'parent_id' => $section->id,
            'type' => 'item',
            'title' => 'Users',
            'icon' => 'bi bi-people-fill',
            'route_name' => 'admin.users.index',
            'route_pattern' => 'admin.users.*',
            'permission' => 'users.view',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('bi bi-people-fill');
    }
}
