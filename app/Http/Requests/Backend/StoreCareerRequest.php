<?php

namespace App\Http\Requests\Backend;

use App\Models\Career;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCareerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                 => 'required|string|max:200',
            'department'            => 'nullable|string|max:150',
            'location'              => 'nullable|string|max:150',
            'employment_type'       => ['required', Rule::in(array_keys(Career::EMPLOYMENT_TYPES))],
            'experience'            => 'nullable|string|max:100',
            'salary_info'           => 'nullable|string|max:150',
            'short_description'     => 'nullable|string|max:500',
            'description'           => 'nullable|string',
            'responsibilities'      => 'nullable|string',
            'requirements'          => 'nullable|string',
            'benefits'              => 'nullable|string',
            'application_deadline'  => 'nullable|date',
            'status'                => 'required|boolean',
            'is_featured'           => 'nullable|boolean',
            'seo_title'             => 'nullable|string|max:255',
            'meta_description'      => 'nullable|string|max:500',
        ];
    }
}
