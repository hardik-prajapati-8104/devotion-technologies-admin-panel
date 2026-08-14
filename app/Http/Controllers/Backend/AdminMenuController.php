<?php

namespace App\Http\Controllers\Backend; 

use App\Http\Controllers\Controller;
use App\Models\AdminMenu;
use App\Services\AdminMenuBadgeResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdminMenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:menus.view')->only(['index']);
        $this->middleware('can:menus.create')->only(['create', 'store']);
        $this->middleware('can:menus.edit')->only(['edit', 'update']);
        $this->middleware('can:menus.delete')->only(['destroy']);
    }

    public function index()
    {
        $menus = AdminMenu::query()
            ->with('parent')
            ->orderByRaw('COALESCE(parent_id, 0) ASC')
            ->orderBy('sort_order')
            ->get();

        return view('backend.menus.index', compact('menus'));
    }

    public function create()
    {
        $parents = AdminMenu::topLevel()->orderBy('sort_order')->get();
        $routeNames = collect(RouteFacade::getRoutes())->map(fn ($r) => $r->getName())->filter()->sort()->values();
        $badgeKeys = AdminMenuBadgeResolver::availableKeys();

        return view('backend.menus.create', compact('parents', 'routeNames', 'badgeKeys'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $menu = AdminMenu::create($data);

        $this->syncPermissionForMenu($menu);

        return redirect()->route('admin.menus.index')->with('success', 'Menu item created.');
    }

    public function edit(AdminMenu $menu)
    {
        $parents = AdminMenu::topLevel()->where('id', '!=', $menu->id)->orderBy('sort_order')->get();
        $routeNames = collect(RouteFacade::getRoutes())->map(fn ($r) => $r->getName())->filter()->sort()->values();
        $badgeKeys = AdminMenuBadgeResolver::availableKeys();

        return view('backend.menus.edit', compact('menu', 'parents', 'routeNames', 'badgeKeys'));
    }

    public function update(Request $request, AdminMenu $menu)
    {
        $data = $this->validateData($request, $menu->id);

        $menu->update($data);

        $this->syncPermissionForMenu($menu);

        return redirect()->route('admin.menus.index')->with('success', 'Menu item updated.');
    }

    public function destroy(AdminMenu $menu)
    {
        // Children cascade to parent_id = null via nullOnDelete unless you'd rather delete them too
        $menu->children()->delete();
        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', 'Menu item deleted.');
    }

    /**
     * AJAX endpoint for a drag-and-drop reorder UI.
     * Expects: { items: [{id: 1, sort_order: 0, parent_id: null}, ...] }
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:admin_menus,id',
            'items.*.sort_order' => 'required|integer|min:0',
            'items.*.parent_id' => 'nullable|exists:admin_menus,id',
        ]);

        foreach ($request->items as $item) {
            AdminMenu::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order'],
                'parent_id' => $item['parent_id'] ?? null,
            ]);
        }

        AdminMenu::flushCache();

        return response()->json(['success' => true]);
    }

    /**
     * Ensures the menu's `permission` string exists as a Permission record,
     * and that role ID 1 (super-admin) always has it — so newly added
     * menu items are automatically visible/allowed for that role without
     * a manual step in the permissions UI.
     */
    /**
     * Ensures the CRUD permission set (view, create, edit, delete) for this
     * menu's resource exists as Permission records, and that role ID 1
     * (super-admin) always has all of them — so a newly added menu item
     * is automatically fully permitted for that role without a manual step.
     */
    protected function syncPermissionForMenu(AdminMenu $menu): void
    {
        if (empty($menu->permission)) {
            return;
        }

        // Accepts either "clients" or "clients.view" typed into the form —
        // strips a trailing .view/.create/.edit/.delete to get the base resource name.
        $base = preg_replace('/\.(view|create|edit|delete)$/', '', $menu->permission);

        $actions = ['view', 'create', 'edit', 'delete'];

        $role = Role::find(1);

        foreach ($actions as $action) {
            $permission = Permission::firstOrCreate([
                'name'       => "{$base}.{$action}",
                'guard_name' => 'admin', // match your admin auth guard
            ]);

            if ($role && ! $role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'parent_id' => ['nullable', 'exists:admin_menus,id', Rule::notIn([$ignoreId])],
            'type' => 'required|in:section,item',
            'title' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'route_name' => 'nullable|string|max:255',
            'route_pattern' => 'nullable|string|max:255',
            'permission' => 'nullable|string|max:255',
            'badge_key' => 'nullable|string|max:100',
            'open_in_new_tab' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
    }
}