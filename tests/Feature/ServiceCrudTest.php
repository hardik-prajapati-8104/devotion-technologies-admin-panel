<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ServiceCrudTest extends TestCase
{
    use RefreshDatabase;

    private function adminWithPermissions(array $permissions): Admin
    {
        $admin = Admin::factory()->create();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin']);
        }

        $admin->givePermissionTo($permissions);

        return $admin;
    }

    public function test_admin_without_permission_cannot_view_services(): void
    {
        $admin = Admin::factory()->create(); // no permissions granted

        $response = $this->actingAs($admin, 'admin')->get(route('admin.services.index'));

        $response->assertForbidden();
    }

    public function test_admin_with_permission_can_view_services(): void
    {
        $admin = $this->adminWithPermissions(['services.view']);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.services.index'));

        $response->assertOk();
    }

    public function test_admin_can_create_a_service_with_valid_data(): void
    {
        $admin = $this->adminWithPermissions(['services.view', 'services.create']);
        $category = ServiceCategory::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.services.store'), [
            'name'                 => 'Test Service',
            'service_category_id'  => $category->id,
            'status'               => 1,
        ]);

        $response->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseHas('services', ['name' => 'Test Service', 'slug' => 'test-service']);
    }

    public function test_service_creation_fails_without_required_name(): void
    {
        $admin = $this->adminWithPermissions(['services.view', 'services.create']);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.services.store'), [
            'status' => 1,
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertDatabaseCount('services', 0);
    }

    public function test_duplicate_service_names_get_unique_slugs(): void
    {
        $admin = $this->adminWithPermissions(['services.view', 'services.create']);

        $this->actingAs($admin, 'admin')->post(route('admin.services.store'), [
            'name' => 'Web Design', 'status' => 1,
        ]);
        $this->actingAs($admin, 'admin')->post(route('admin.services.store'), [
            'name' => 'Web Design', 'status' => 1,
        ]);

        $this->assertDatabaseHas('services', ['slug' => 'web-design']);
        $this->assertDatabaseHas('services', ['slug' => 'web-design-2']);
    }

    public function test_deleting_a_service_soft_deletes_it(): void
    {
        $admin = $this->adminWithPermissions(['services.view', 'services.delete']);
        $service = Service::factory()->create();

        $response = $this->actingAs($admin, 'admin')->delete(route('admin.services.destroy', $service->id));

        $response->assertRedirect();
        $this->assertSoftDeleted('services', ['id' => $service->id]);
    }
}
