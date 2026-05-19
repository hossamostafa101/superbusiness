<?php

namespace App\Http\Requests\App;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],

            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'birthdate' => ['nullable', 'date'],

            'source' => ['nullable', 'string', 'max:80'],

            'status' => [
                'required',
                Rule::in(['active', 'inactive', 'blocked']),
            ],

            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'source' => $this->input('source', 'manual'),
            'status' => $this->input('status', 'active'),
        ]);
    }
}