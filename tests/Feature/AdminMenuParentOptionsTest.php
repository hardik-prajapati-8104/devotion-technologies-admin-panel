<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\AdminMenu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AdminMenuParentOptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_page_lists_section_parents_for_submenu_assignment(): void
    {
        $admin = Admin::factory()->create();

        foreach (['menus.view', 'menus.create', 'menus.edit', 'menus.delete'] as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        $admin->givePermissionTo(['menus.view', 'menus.create', 'menus.edit', 'menus.delete']);

        AdminMenu::create([
            'type' => 'section',
            'title' => 'Recruitment',
            'sort_order' => 0,
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.menus.create'));

        $response->assertOk();
        $response->assertSee('Recruitment');
    }
}
