<?php

namespace App\Http\Requests\Backend;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_category_id' => 'nullable|exists:project_categories,id',
            'name'                => 'required|string|max:150',
            'client_name'         => 'nullable|string|max:150',
            'location'            => 'nullable|string|max:150',
            'short_description'   => 'nullable|string|max:500',
            'full_description'    => 'nullable|string',
            'featured_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery.*'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'project_url'         => 'nullable|url|max:255',
            'technologies'        => 'nullable|string|max:500',
            'completion_date'     => 'nullable|date',
            'status'              => ['required', Rule::in(array_keys(Project::STATUSES))],
            'is_featured'         => 'nullable|boolean',
            'display_order'       => 'nullable|integer|min:0',
            'seo_title'           => 'nullable|string|max:255',
            'meta_description'    => 'nullable|string|max:500',
            'og_image'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}
