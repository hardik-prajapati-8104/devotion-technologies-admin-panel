<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function __construct(private ImageUploadService $imageUploadService)
    {
    }

    public function edit()
    {
        $admin = Auth::guard('admin')->user();

        return view('backend.profile.edit', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $data = $request->validate([
            'first_name'    => 'required|string|max:50',
            'last_name'     => 'required|string|max:50',
            'email'         => ['required', 'email', 'max:100', Rule::unique('admins', 'email')->ignore($admin->id)],
            'mobile_number' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $admin->first_name    = $data['first_name'];
        $admin->last_name     = $data['last_name'];
        $admin->email         = $data['email'];
        $admin->mobile_number = $data['mobile_number'] ?? null;

        if ($request->hasFile('profile_image')) {
            $admin->profile_image = $this->imageUploadService->replace(
                $request->file('profile_image'),
                'admins',
                $admin->profile_image
            );
        }

        $admin->save();

        ActivityLog::record('updated', 'Profile', $admin->id, 'Updated own profile.');

        session()->flash('success', 'Your profile has been updated !!');
        return back();
    }

    public function changePassword()
    {
        return view('backend.profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        if (! Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Your current password does not match.']);
        }

        $admin->password = Hash::make($request->password);
        $admin->save();

        ActivityLog::record('updated', 'Profile', $admin->id, 'Changed own password.');

        session()->flash('success', 'Your password has been changed !!');
        return back();
    }
}
