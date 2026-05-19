<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkspaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check()
            && auth('admin')->user()->can('workspaces.create');
    }

    public function rules(): array
    {
        return [
            'owner_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],

            'name' => ['required', 'string', 'max:150'],

            'slug' => [
                'required',
                'string',
                'max:160',
                'alpha_dash',
                Rule::unique('workspaces', 'slug'),
            ],

            'type' => ['required', 'string', 'max:60'],

            'status' => [
                'required',
                Rule::in(['active', 'pending', 'suspended', 'cancelled']),
            ],

            'trial_ends_at' => ['nullable', 'date'],
        ];
    }
}