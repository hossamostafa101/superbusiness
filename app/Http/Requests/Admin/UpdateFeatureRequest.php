<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFeatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check()
            && auth('admin')->user()->can('features.edit');
    }

    public function rules(): array
    {
        $featureId = $this->route('feature')?->id;

        return [
            'name' => ['required', 'string', 'max:150'],

            'key' => [
                'required',
                'string',
                'max:150',
                'alpha_dash',
                Rule::unique('features', 'key')->ignore($featureId),
            ],

            'description' => ['nullable', 'string'],

            'type' => [
                'required',
                Rule::in(['limit', 'boolean', 'text']),
            ],

            'module' => ['nullable', 'string', 'max:100'],

            'is_active' => ['nullable', 'boolean'],

            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }
}