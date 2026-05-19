<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check()
            && auth('admin')->user()->can('subscriptions.edit');
    }

    public function rules(): array
    {
        return [
            'plan_id' => [
                'required',
                'integer',
                Rule::exists('plans', 'id'),
            ],

            'status' => [
                'required',
                Rule::in(['trialing', 'active', 'past_due', 'cancelled', 'expired']),
            ],

            'billing_cycle' => [
                'required',
                Rule::in(['monthly', 'yearly']),
            ],

            'starts_at' => ['nullable', 'date'],
            'trial_ends_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
        ];
    }
}