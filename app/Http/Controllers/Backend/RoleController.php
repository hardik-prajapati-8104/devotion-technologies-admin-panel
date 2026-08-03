<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('roles.view')) {
            abort(403, 'Sorry !! You are unauthorized to view any role !');
        }

        $roles = Role::where('guard_name', 'admin')
            ->withCount('permissions')
            ->get()
            ->map(function (Role $role) {
                // Spatie's Role model has no generic "users" relation since
                // it's model-agnostic — count assigned Admins directly.
                $role->users_count = Admin::role($role->name)->count();

                return $role;
            })
            ->values();

        return view('backend.roles.index', compact('roles'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('roles.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any role !');
        }

        $permissions = Permission::where('guard_name', 'admin')->get()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });

        return view('backend.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('roles.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any role !');
        }

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:50', Rule::unique('roles', 'name')],
            'permissions'   => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'admin']);
        $role->syncPermissions($data['permissions']);

        Cache::forget('admin_roles');
        ActivityLog::record('created', 'Roles', $role->id, "Created role \"{$role->name}\".");

        session()->flash('success', $role->name.' role has been created !!');
        return redirect()->route('admin.roles.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('roles.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any role !');
        }

        $role = Role::findOrFail($id);
        $permissions = Permission::where('guard_name', 'admin')->get()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
        $assigned = $role->permissions->pluck('name')->all();

        return view('backend.roles.edit', compact('role', 'permissions', 'assigned'));
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('roles.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any role !');
        }

        $role = Role::findOrFail($id);

        if (in_array($role->name, ['superadmin'], true)) {
            session()->flash('error', 'The Super Admin role cannot be modified.');
            return back();
        }

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:50', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions'   => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->name = $data['name'];
        $role->save();
        $role->syncPermissions($data['permissions']);

        Cache::forget('admin_roles');
        ActivityLog::record('updated', 'Roles', $role->id, "Updated role \"{$role->name}\".");

        session()->flash('success', $role->name.' role has been updated !!');
        return redirect()->route('admin.roles.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('roles.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete any role !');
        }

        $role = Role::findOrFail($id);

        if (in_array($role->name, ['superadmin'], true)) {
            session()->flash('error', 'The Super Admin role cannot be deleted.');
            return back();
        }

        if (Admin::role($role->name)->count() > 0) {
            session()->flash('error', 'This role is still assigned to one or more users and cannot be deleted.');
            return back();
        }

        $role->delete();

        Cache::forget('admin_roles');
        ActivityLog::record('deleted', 'Roles', $id, "Deleted role \"{$role->name}\".");

        session()->flash('success', 'Role has been deleted !!');
        return back();
    }
}
