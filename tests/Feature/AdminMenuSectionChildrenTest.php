<?php

namespace Tests\Feature;

use App\Models\AdminMenu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMenuSectionChildrenTest extends TestCase
{
    use RefreshDatabase;

    public function test_section_parent_can_hold_child_menu_items(): void
    {
        $section = AdminMenu::create([
            'type' => 'section',
            'title' => 'System',
            'sort_order' => 0,
        ]);

        $child = AdminMenu::create([
            'parent_id' => $section->id,
            'type' => 'item',
            'title' => 'Careers',
            'route_name' => 'admin.careers.index',
            'route_pattern' => 'admin.careers.*',
            'sort_order' => 0,
        ]);

        $tree = AdminMenu::tree();

        $this->assertTrue($tree->contains('id', $section->id));
        $this->assertTrue($tree->firstWhere('id', $section->id)->children->contains('id', $child->id));
    }
}
