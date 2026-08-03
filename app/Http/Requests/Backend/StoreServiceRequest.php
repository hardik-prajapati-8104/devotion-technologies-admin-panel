<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_category_id' => 'nullable|exists:service_categories,id',
            'name'                => 'required|string|max:150',
            'short_description'   => 'nullable|string|max:500',
            'full_description'    => 'nullable|string',
            'featured_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'icon'                => 'nullable|string|max:100',
            'display_order'       => 'nullable|integer|min:0',
            'is_featured'         => 'nullable|boolean',
            'status'              => 'required|boolean',
            'seo_title'           => 'nullable|string|max:255',
            'meta_description'    => 'nullable|string|max:500',
            'og_image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}
