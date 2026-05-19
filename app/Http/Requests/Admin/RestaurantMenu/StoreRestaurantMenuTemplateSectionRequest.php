<?php

namespace App\Http\Requests\Admin\RestaurantMenu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRestaurantMenuTemplateSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        return [
            'section_type' => [
                'required',
                Rule::in([
                    'hero',
                    'branch_switch',
                    'category_tabs',
                    'items',
                    'item_modal',
                    'cart',
                    'invoice',
                    'footer',
                ]),
            ],

            'name' => ['required', 'string', 'max:120'],
            'key' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/', 'unique:restaurant_menu_template_sections,key'],
            'description' => ['nullable', 'string', 'max:3000'],

            'preview_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'view' => ['required', 'string', 'max:120'],

            'is_premium' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_premium' => $this->boolean('is_premium'),
            'is_active' => $this->boolean('is_active'),
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }
}