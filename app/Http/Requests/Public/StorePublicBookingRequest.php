<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePublicBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $workspace = $this->route('workspace');

        return [
            'service_id' => [
                'nullable',
                'integer',
                Rule::exists('business_services', 'id')
                    ->where('workspace_id', $workspace?->id)
                    ->where('is_active', 1),
            ],

            'customer_name' => ['required', 'string', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'customer_email' => ['nullable', 'email', 'max:255'],

            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],

            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'اسمك مطلوب.',
            'customer_phone.required' => 'رقم الهاتف مطلوب.',
            'appointment_date.required' => 'تاريخ الموعد مطلوب.',
            'appointment_date.after_or_equal' => 'لا يمكن اختيار تاريخ قديم.',
            'start_time.required' => 'وقت الموعد مطلوب.',
        ];
    }
}