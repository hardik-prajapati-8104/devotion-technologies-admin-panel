<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreEnquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => 'required|string|max:150',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:3000',
            'source'  => 'nullable|string|max:100',
            'website' => 'nullable|max:0', // honeypot
        ];
    }

    public function messages(): array
    {
        return [
            'website.max' => 'Submission rejected.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (trim((string) $this->input('website')) !== '') {
                $validator->errors()->add('website', 'Submission rejected.');
            }
        });
    }
}
