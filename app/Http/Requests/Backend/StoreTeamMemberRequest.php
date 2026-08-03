<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:150',
            'designation'    => 'nullable|string|max:150',
            'department'     => 'nullable|string|max:150',
            'profile_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'biography'      => 'nullable|string|max:3000',
            'email'          => 'nullable|email|max:150',
            'phone'          => 'nullable|string|max:30',
            'linkedin_url'   => 'nullable|url|max:255',
            'facebook_url'   => 'nullable|url|max:255',
            'instagram_url'  => 'nullable|url|max:255',
            'twitter_url'    => 'nullable|url|max:255',
            'display_order'  => 'nullable|integer|min:0',
            'status'         => 'required|boolean',
        ];
    }
}
