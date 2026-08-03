<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'career_id'     => 'nullable|exists:careers,id',
            'applicant_name' => 'required|string|max:150',
            'email'         => 'required|email|max:150',
            'phone'         => 'nullable|string|max:30',
            'resume'        => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB
            'cover_letter'  => 'nullable|string|max:3000',
            // Honeypot: a hidden field named "website" that real applicants never see or fill.
            // Any value here means a bot filled every field on the form.
            'website'       => 'nullable|max:0',
        ];
    }

    public function messages(): array
    {
        return [
            'website.max' => 'Submission rejected.', // generic — never reveal the honeypot exists
        ];
    }

    /**
     * Extra safety net beyond the "max:0" rule above, in case a bot
     * sends whitespace instead of nothing.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (trim((string) $this->input('website')) !== '') {
                $validator->errors()->add('website', 'Submission rejected.');
            }
        });
    }
}
