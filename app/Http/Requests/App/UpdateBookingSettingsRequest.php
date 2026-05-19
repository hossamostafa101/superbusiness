<?php

namespace App\Http\Requests\App;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        return [
            'booking_enabled' => ['nullable', 'boolean'],

            'booking_days' => ['nullable', 'array'],
            'booking_days.*' => [
                'string',
                Rule::in(['sat', 'sun', 'mon', 'tue', 'wed', 'thu', 'fri']),
            ],

            'booking_start_time' => ['required', 'date_format:H:i'],
            'booking_end_time' => ['required', 'date_format:H:i', 'after:booking_start_time'],

            'booking_slot_interval' => ['required', 'integer', 'min:5', 'max:240'],
            'booking_advance_days' => ['required', 'integer', 'min:1', 'max:365'],
            'booking_buffer_minutes' => ['required', 'integer', 'min:0', 'max:240'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'booking_enabled' => $this->boolean('booking_enabled'),
        ]);
    }
}