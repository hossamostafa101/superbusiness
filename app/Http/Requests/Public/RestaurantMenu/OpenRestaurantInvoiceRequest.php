<?php

namespace App\Http\Requests\Public\RestaurantMenu;

use Illuminate\Foundation\Http\FormRequest;

class OpenRestaurantInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'table_code' => ['nullable', 'string', 'max:80'],
            'table_number' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'اسم العميل مطلوب.',
            'customer_phone.required' => 'رقم الهاتف مطلوب.',
        ];
    }
}