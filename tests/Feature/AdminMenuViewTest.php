<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AdminMenuViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_menu_management_page_renders_content(): void
    {
        $admin = Admin::factory()->create();

        foreach (['menus.view', 'menus.create', 'menus.edit', 'menus.delete'] as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        $admin->givePermissionTo(['menus.view', 'menus.create', 'menus.edit', 'menus.delete']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.menus.index'));

        $response->assertOk();
        $response->assertSee('Admin Menu Management');
    }
}
