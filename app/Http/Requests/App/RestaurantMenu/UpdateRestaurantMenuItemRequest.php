<?php

namespace App\Http\Requests\App\RestaurantMenu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRestaurantMenuItemRequest extends FormRequest
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

            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('restaurant_menu_categories', 'id')
                    ->where('workspace_id', $workspace?->id),
            ],

            'name' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:5000'],

            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_image' => ['nullable', 'boolean'],

            'calories' => ['nullable', 'integer', 'min:0'],
            'preparation_time_minutes' => ['nullable', 'integer', 'min:0', 'max:1440'],

            'is_available' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'currency' => $this->input('currency', 'EGP'),
            'is_available' => $this->boolean('is_available'),
            'is_featured' => $this->boolean('is_featured'),
            'remove_image' => $this->boolean('remove_image'),
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }
}