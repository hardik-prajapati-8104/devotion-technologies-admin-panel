<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreSubscriberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'   => ['required', 'email', 'max:150'],
            'name'    => 'nullable|string|max:150',
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
