<?php

namespace App\Http\Requests\App\RestaurantMenu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRestaurantMenuContentSectionRequest extends FormRequest
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
                'nullable',
                'integer',
                Rule::exists('restaurant_branches', 'id')
                    ->where('workspace_id', $workspace?->id),
            ],

            'type' => [
                'required',
                Rule::in([
                    'featured_items',
                    'item_collection',
                    'offers_slider',
                ]),
            ],

            'title' => ['required', 'string', 'max:160'],
            'subtitle' => ['nullable', 'string', 'max:255'],

            'background_type' => [
                'required',
                Rule::in(['solid', 'gradient']),
            ],

            'background_color' => ['nullable', 'string', 'max:30'],
            'background_gradient_from' => ['nullable', 'string', 'max:30'],
            'background_gradient_to' => ['nullable', 'string', 'max:30'],
            'text_color' => ['nullable', 'string', 'max:30'],
            'button_color' => ['nullable', 'string', 'max:30'],

            'layout' => ['nullable', 'string', 'max:60'],

            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],

            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],

            'item_ids' => ['nullable', 'array'],
            'item_ids.*' => [
                'integer',
                Rule::exists('restaurant_menu_items', 'id')
                    ->where('workspace_id', $workspace?->id),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'sort_order' => $this->input('sort_order', 0),
            'layout' => $this->input('layout', 'horizontal_scroll'),
            'background_color' => $this->input('background_color', '#ffffff'),
            'text_color' => $this->input('text_color', '#111827'),
            'button_color' => $this->input('button_color', '#2563eb'),
        ]);
    }
}