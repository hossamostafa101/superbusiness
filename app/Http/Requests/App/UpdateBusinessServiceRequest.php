<?php

namespace App\Http\Requests\App;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],

            'duration_minutes' => ['required', 'integer', 'min:5', 'max:1440'],

            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],

            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'duration_minutes' => $this->input('duration_minutes', 30),
            'currency' => $this->input('currency', 'EGP'),
            'sort_order' => $this->input('sort_order', 0),
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}