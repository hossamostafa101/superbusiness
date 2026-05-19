<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check()
            && auth('admin')->user()->can('plans.create');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],

            'slug' => [
                'required',
                'string',
                'max:120',
                'alpha_dash',
                Rule::unique('plans', 'slug'),
            ],

            'description' => ['nullable', 'string'],

            'monthly_price' => ['required', 'numeric', 'min:0'],
            'yearly_price' => ['nullable', 'numeric', 'min:0'],

            'currency' => ['required', 'string', 'max:10'],

            'is_free' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],

            'sort_order' => ['nullable', 'integer', 'min:0'],

            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_free' => $this->boolean('is_free'),
            'is_active' => $this->boolean('is_active'),
            'is_featured' => $this->boolean('is_featured'),
            'sort_order' => $this->input('sort_order', 0),
            'currency' => $this->input('currency', 'EGP'),
        ]);
    }
}