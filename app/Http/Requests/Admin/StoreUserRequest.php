<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check()
            && auth('admin')->user()->can('users.create');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('users', 'phone'),
            ],

            'password' => ['required', 'string', 'min:8', 'confirmed'],

            'status' => ['required', Rule::in(['active', 'pending', 'suspended'])],

            'roles' => ['nullable', 'array'],
            'roles.*' => [
                'integer',
                Rule::exists('roles', 'id')->where('guard_name', 'admin'),
            ],

            'job_title' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}