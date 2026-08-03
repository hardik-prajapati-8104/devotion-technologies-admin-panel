<?php

namespace App\Http\Requests\Backend;

use App\Models\Blog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'blog_category_id'  => 'nullable|exists:blog_categories,id',
            'title'             => 'required|string|max:200',
            'short_description' => 'nullable|string|max:500',
            'content'           => 'nullable|string',
            'featured_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'thumbnail'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tags'              => 'nullable|string|max:500', // comma-separated tag names
            'reading_time'      => 'nullable|integer|min:1|max:180',
            'publish_date'      => 'nullable|date',
            'status'            => ['required', Rule::in(array_keys(Blog::STATUSES))],
            'is_featured'       => 'nullable|boolean',

            'seo_title'         => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
            'focus_keyword'     => 'nullable|string|max:150',
            'canonical_url'     => 'nullable|url|max:255',
            'og_title'          => 'nullable|string|max:255',
            'og_description'    => 'nullable|string|max:500',
            'og_image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status must be one of: draft, published, scheduled.',
        ];
    }
}
