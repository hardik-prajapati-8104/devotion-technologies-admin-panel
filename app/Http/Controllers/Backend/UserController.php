<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreUserRequest;
use App\Http\Requests\Backend\UpdateUserRequest;
use App\Models\ActivityLog;
use App\Models\Admin;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public $user;

    public function __construct(private ImageUploadService $imageUploadService)
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    /**
     * Display a listing of admin users.
     */
    public function index(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('users.view')) {
            abort(403, 'Sorry !! You are unauthorized to view any user !');
        }

        $users = Cache::remember('admin_users', 10, function () {
            return Admin::with('roles')->latest()->get();
        });

        return view('backend.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('users.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any user !');
        }

        $roles = Role::where('guard_name', 'admin')->get();

        return view('backend.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(StoreUserRequest $request)
    {
        if (is_null($this->user) || ! $this->user->can('users.create')) {
            abort(403, 'Sorry !! You are unauthorized to create any user !');
        }

        $data = $request->validated();

        $admin = new Admin();
        $admin->first_name    = $data['first_name'];
        $admin->last_name     = $data['last_name'];
        $admin->username      = $data['username'];
        $admin->email         = $data['email'];
        $admin->mobile_number = $data['mobile_number'] ?? null;
        $admin->password      = Hash::make($data['password']);
        $admin->status        = $data['status'];
        $admin->login         = $data['login'];

        if ($request->hasFile('profile_image')) {
            $admin->profile_image = $this->imageUploadService->upload($request->file('profile_image'), 'admins');
        }

        $admin->save();
        $admin->assignRole($data['roles']);

        Cache::forget('admin_users');
        ActivityLog::record('created', 'Users', $admin->id, "Created user \"{$admin->name}\".");

        session()->flash('success', $admin->username.' has been created !!');
        return redirect()->route('admin.users.index');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('users.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any user !');
        }

        $admin = Admin::findOrFail($id);
        $roles = Role::where('guard_name', 'admin')->get();

        return view('backend.users.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('users.edit')) {
            abort(403, 'Sorry !! You are unauthorized to edit any user !');
        }

        $admin = Admin::findOrFail($id);
        $data = $request->validated();

        $admin->first_name    = $data['first_name'];
        $admin->last_name     = $data['last_name'];
        $admin->username      = $data['username'];
        $admin->email         = $data['email'];
        $admin->mobile_number = $data['mobile_number'] ?? null;
        $admin->status        = $data['status'];
        $admin->login         = $data['login'];

        if ($request->filled('password')) {
            $admin->password = Hash::make($data['password']);
        }

        if ($request->hasFile('profile_image')) {
            $admin->profile_image = $this->imageUploadService->replace(
                $request->file('profile_image'),
                'admins',
                $admin->profile_image
            );
        }

        $admin->save();

        $admin->syncRoles($data['roles']);

        Cache::forget('admin_users');
        ActivityLog::record('updated', 'Users', $admin->id, "Updated user \"{$admin->name}\".");

        session()->flash('success', $admin->username.' has been updated !!');
        return redirect()->route('admin.users.index');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('users.delete')) {
            abort(403, 'Sorry !! You are unauthorized to delete any user !');
        }

        if ($id === $this->user->id) {
            session()->flash('error', 'You cannot delete your own account while logged in.');
            return back();
        }

        $admin = Admin::find($id);

        if ($admin && $admin->hasRole('superadmin') && Admin::role('superadmin')->count() <= 1) {
            session()->flash('error', 'Sorry !! At least one Super Admin must remain in the system.');
            return back();
        }

        if (! is_null($admin)) {
            $this->imageUploadService->delete($admin->profile_image);
            $admin->delete();

            Cache::forget('admin_users');
            ActivityLog::record('deleted', 'Users', $id, "Deleted user \"{$admin->name}\".");
        }

        session()->flash('success', 'User has been deleted !!');
        return back();
    }

    /**
     * Toggle a user's active status via AJAX (used by the status-badge switch).
     */
    public function toggleStatus(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('users.edit')) {
            abort(403);
        }

        $admin = Admin::findOrFail($id);
        $admin->status = ! $admin->status;
        $admin->save();

        Cache::forget('admin_users');

        return response()->json([
            'success' => true,
            'status'  => $admin->status,
            'message' => 'Status updated successfully.',
        ]);
    }
}
