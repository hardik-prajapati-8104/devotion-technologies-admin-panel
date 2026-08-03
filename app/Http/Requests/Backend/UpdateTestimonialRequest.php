<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_name'    => 'required|string|max:150',
            'company'        => 'nullable|string|max:150',
            'designation'    => 'nullable|string|max:150',
            'profile_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'rating'         => ['required', 'integer', Rule::in([1, 2, 3, 4, 5])],
            'testimonial'    => 'required|string|max:2000',
            'display_order'  => 'nullable|integer|min:0',
            'is_featured'    => 'nullable|boolean',
            'status'         => 'required|boolean',
        ];
    }
}
