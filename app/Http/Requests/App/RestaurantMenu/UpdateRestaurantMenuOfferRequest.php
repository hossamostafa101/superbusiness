<?php

namespace App\Http\Requests\App\RestaurantMenu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRestaurantMenuOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        $workspace = $this->route('workspace');

        return [
            'item_id' => [
                'nullable',
                'integer',
                Rule::exists('restaurant_menu_items', 'id')
                    ->where('workspace_id', $workspace?->id),
            ],

            'title' => ['required', 'string', 'max:160'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],

            'badge_text' => ['nullable', 'string', 'max:80'],

            'old_price' => ['nullable', 'numeric', 'min:0'],
            'new_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:10'],

            'button_text' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:255'],

            'background_color' => ['nullable', 'string', 'max:30'],
            'text_color' => ['nullable', 'string', 'max:30'],

            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],

            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],

            
            'is_orderable' => ['nullable', 'boolean'],
'order_mode' => ['required', 'in:standalone,single_item,bundle'],
'button_action' => ['nullable', 'in:none,open_offer,open_item,external_url'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'sort_order' => $this->input('sort_order', 0),
            'currency' => $this->input('currency', 'EGP'),
            'background_color' => $this->input('background_color', '#111827'),
            'text_color' => $this->input('text_color', '#ffffff'),


            
            'is_orderable' => $this->boolean('is_orderable'),
'order_mode' => $this->input('order_mode', 'standalone'),
'button_action' => $this->input('button_action', 'open_offer'),
        ]);
    }
}