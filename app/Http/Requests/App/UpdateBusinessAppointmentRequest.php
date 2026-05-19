<?php

namespace App\Http\Requests\App;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        $workspace = $this->route('workspace');

        return [
            'customer_id' => [
                'nullable',
                'integer',
                Rule::exists('business_customers', 'id')
                    ->where('workspace_id', $workspace?->id),
            ],

            'service_id' => [
                'nullable',
                'integer',
                Rule::exists('business_services', 'id')
                    ->where('workspace_id', $workspace?->id),
            ],

            'customer_name' => ['nullable', 'string', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:40'],
            'customer_email' => ['nullable', 'email', 'max:255'],

            'appointment_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],

            'status' => [
                'required',
                Rule::in(['pending', 'confirmed', 'completed', 'cancelled', 'no_show']),
            ],

            'source' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->input('status', 'pending'),
            'source' => $this->input('source', 'manual'),
        ]);
    }
}