<?php

namespace App\Http\Requests\Billing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        return [
            'billing_cycle' => [
                'required',
                Rule::in(['monthly', 'yearly']),
            ],

            'provider' => [
                'required',
                Rule::in(['manual', 'kashier', 'paddle']),
            ],

            'receipt_image' => [
                Rule::requiredIf($this->input('provider') === 'manual'),
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'receipt_image.required' => 'يجب رفع صورة إثبات الدفع عند اختيار الدفع اليدوي.',
            'receipt_image.image' => 'ملف إثبات الدفع يجب أن يكون صورة.',
            'receipt_image.max' => 'حجم صورة إثبات الدفع يجب ألا يتجاوز 4 ميجابايت.',
        ];
    }
}