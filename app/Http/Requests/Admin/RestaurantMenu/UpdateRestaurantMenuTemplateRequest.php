<?php

namespace App\Http\Requests\Admin\RestaurantMenu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRestaurantMenuTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        $template = $this->route('restaurantMenuTemplate');

        return [
            'name' => ['required', 'string', 'max:120'],
            'key' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('restaurant_menu_templates', 'key')->ignore($template?->id),
            ],
            'description' => ['nullable', 'string', 'max:3000'],

            'preview_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'hero' => ['required', 'string', 'max:100'],
            'branch_switch' => ['required', 'string', 'max:100'],
            'category_tabs' => ['required', 'string', 'max:100'],
            'items' => ['required', 'string', 'max:100'],
            'item_modal' => ['required', 'string', 'max:100'],
            'cart' => ['required', 'string', 'max:100'],
            'invoice' => ['required', 'string', 'max:100'],
            'footer' => ['required', 'string', 'max:100'],

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