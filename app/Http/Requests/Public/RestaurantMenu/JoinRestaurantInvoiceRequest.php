<?php

namespace App\Http\Requests\Public\RestaurantMenu;

use Illuminate\Foundation\Http\FormRequest;

class JoinRestaurantInvoiceRequest extends FormRequest
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
            'pin' => ['required', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'اسم العميل مطلوب.',
            'customer_phone.required' => 'رقم الهاتف مطلوب.',
            'pin.required' => 'رقم PIN مطلوب.',
        ];
    }
}