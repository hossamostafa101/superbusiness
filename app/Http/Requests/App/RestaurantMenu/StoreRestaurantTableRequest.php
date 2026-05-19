<?php

namespace App\Http\Requests\App\RestaurantMenu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRestaurantTableRequest extends FormRequest
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

            'name' => ['required', 'string', 'max:120'],

            'number' => [
                'required',
                'string',
                'max:50',
            ],

            'seats' => ['nullable', 'integer', 'min:1', 'max:100'],
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