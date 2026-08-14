<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_name'      => ['required', 'string', 'max:255'],
            'company_name'     => ['required', 'string', 'max:255'],
            'company_website'  => ['nullable', 'url', 'max:255'],
            'company_email'    => ['nullable', 'email', 'max:255'],
            'company_contact'  => ['nullable', 'string', 'max:30'],
            'company_logo'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'sort_order'       => ['nullable', 'integer', 'min:0'],
            'status'           => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'client_name'  => 'client name',
            'company_name' => 'company name',
        ];
    }
}
