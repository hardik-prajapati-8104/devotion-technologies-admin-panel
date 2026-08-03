<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('user');

        return [
            'first_name'     => 'required|string|max:50',
            'last_name'      => 'required|string|max:50',
            'username'       => 'required|string|max:100|unique:admins,username,'.$id,
            'email'          => 'required|string|email|max:100|unique:admins,email,'.$id,
            'mobile_number'  => 'nullable|string|max:20',
            'password'       => 'nullable|string|min:8|confirmed',
            'profile_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'roles'          => 'required|array|min:1',
            'roles.*'        => 'exists:roles,name',
            'status'         => 'required|boolean',
            'login'          => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'roles.required' => 'Please assign at least one role to this user.',
        ];
    }
}
