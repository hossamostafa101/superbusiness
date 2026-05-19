<?php

namespace App\Http\Requests\App;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        $workspace = $this->route('workspace');

        return [
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('business_categories', 'id')
                    ->where('workspace_id', $workspace?->id),
            ],

            'name' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:5000'],

            'price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],

            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_available' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'currency' => $this->input('currency', 'EGP'),
            'sort_order' => $this->input('sort_order', 0),
            'is_available' => $this->boolean('is_available'),
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }
}