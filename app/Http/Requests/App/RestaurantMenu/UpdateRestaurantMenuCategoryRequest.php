<?php

namespace App\Http\Requests\App\RestaurantMenu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRestaurantMenuCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        $workspace = $this->route('workspace');

        return [
            'branch_id' => [
                'required',
                'integer',
                Rule::exists('restaurant_branches', 'id')
                    ->where('workspace_id', $workspace?->id),
            ],

            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:3000'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],

            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sort_order' => $this->input('sort_order', 0),
            'is_active' => $this->boolean('is_active'),
            'remove_image' => $this->boolean('remove_image'),
        ]);
    }
}