<?php

namespace App\Http\Requests\App\RestaurantMenu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRestaurantMenuSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        return [
            'restaurant_ordering_mode' => [
                'required',
                Rule::in(['single_order', 'open_invoice']),
            ],

            'restaurant_invoice_duration_minutes' => [
                'required',
                'integer',
                'min:15',
                'max:1440',
            ],

            'restaurant_invoice_join_policy' => [
                'required',
                Rule::in(['allow_with_pin', 'block_until_closed']),
            ],

            'restaurant_allow_new_invoice_when_table_busy' => [
                'nullable',
                'boolean',
            ],

            'restaurant_invoice_extend_minutes_step' => [
                'required',
                'integer',
                'min:5',
                'max:240',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'restaurant_allow_new_invoice_when_table_busy' => $this->boolean('restaurant_allow_new_invoice_when_table_busy'),
        ]);
    }

    public function messages(): array
    {
        return [
            'restaurant_ordering_mode.required' => 'اختر طريقة استقبال الطلبات.',
            'restaurant_invoice_duration_minutes.required' => 'مدة الفاتورة مطلوبة.',
            'restaurant_invoice_join_policy.required' => 'سياسة الانضمام مطلوبة.',
        ];
    }
}