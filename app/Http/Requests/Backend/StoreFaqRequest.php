<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class StoreFaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'faq_category_id' => 'nullable|exists:faq_categories,id',
            'question'        => 'required|string|max:255',
            'answer'          => 'required|string|max:3000',
            'display_order'   => 'nullable|integer|min:0',
            'status'          => 'required|boolean',
        ];
    }
}
