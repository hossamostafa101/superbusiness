<?php

namespace App\Http\Requests\Public\RestaurantMenu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePublicRestaurantOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $workspace = $this->route('workspace');
        $branch = $this->route('branch');

        return [
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'customer_email' => ['nullable', 'email', 'max:255'],

            'order_type' => [
                'required',
                Rule::in(['dine_in', 'takeaway', 'delivery']),
            ],

            'table_number' => ['nullable', 'string', 'max:50'],
            'delivery_address' => ['nullable', 'string', 'max:3000'],
            'notes' => ['nullable', 'string', 'max:3000'],

            'items' => ['required', 'array', 'min:1'],

            'items.*.item_id' => [
                'required',
                'integer',
                Rule::exists('restaurant_menu_items', 'id')
                    ->where('workspace_id', $workspace?->id)
                    ->where('branch_id', $branch?->id)
                    ->where('is_available', 1),
            ],

            'items.*.variant_id' => [
                'nullable',
                'integer',
                Rule::exists('restaurant_item_variants', 'id')
                    ->where('workspace_id', $workspace?->id)
                    ->where('branch_id', $branch?->id)
                    ->where('is_active', 1),
            ],

            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'items.*.notes' => ['nullable', 'string', 'max:1000'],

            'items.*.options' => ['nullable', 'array'],
            'items.*.options.*' => [
                'integer',
                Rule::exists('restaurant_item_options', 'id')
                    ->where('workspace_id', $workspace?->id)
                    ->where('branch_id', $branch?->id)
                    ->where('is_active', 1),
            ],

            'table_code' => ['nullable', 'string', 'max:80'],
            
            'invoice_id' => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'اسم العميل مطلوب.',
            'customer_phone.required' => 'رقم الهاتف مطلوب.',
            'order_type.required' => 'نوع الطلب مطلوب.',
            'items.required' => 'يجب إضافة صنف واحد على الأقل.',
            'items.min' => 'يجب إضافة صنف واحد على الأقل.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('order_type') === 'delivery' && ! $this->filled('delivery_address')) {
                $validator->errors()->add('delivery_address', 'عنوان التوصيل مطلوب لطلبات الدليفري.');
            }

            if (
    $this->input('order_type') === 'dine_in'
    && ! $this->filled('table_number')
    && ! $this->filled('table_code')
) {
    $validator->errors()->add('table_number', 'رقم الطاولة مطلوب لطلبات داخل المكان.');
}
        });
    }
}