<?php

namespace App\Http\Requests\App\RestaurantMenu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRestaurantBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        $workspace = $this->route('workspace');
        $branch = $this->route('restaurantBranch');

        return [
            'name' => ['required', 'string', 'max:150'],

            'slug' => [
                'nullable',
                'string',
                'max:160',
                'alpha_dash',
                Rule::unique('restaurant_branches', 'slug')
                    ->where('workspace_id', $workspace?->id)
                    ->ignore($branch?->id),
            ],

            'phone' => ['nullable', 'string', 'max:40'],
            'whatsapp_number' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'location_url' => ['nullable', 'url', 'max:2000'],

            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'is_default' => $this->boolean('is_default'),
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }
}