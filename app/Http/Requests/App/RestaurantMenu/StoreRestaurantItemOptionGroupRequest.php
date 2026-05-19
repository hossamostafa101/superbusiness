<?php

namespace App\Http\Requests\App\RestaurantMenu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRestaurantItemOptionGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],

            'type' => [
                'required',
                Rule::in(['single', 'multiple']),
            ],

            'is_required' => ['nullable', 'boolean'],

            'min_choices' => ['nullable', 'integer', 'min:0', 'max:100'],
            'max_choices' => ['nullable', 'integer', 'min:1', 'max:100'],

            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المجموعة مطلوب.',
            'type.required' => 'نوع المجموعة مطلوب.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $type = $this->input('type', 'multiple');

        $this->merge([
            'type' => $type,
            'is_required' => $this->boolean('is_required'),
            'is_active' => $this->boolean('is_active'),
            'sort_order' => $this->input('sort_order', 0),
            'min_choices' => $this->input('min_choices', 0),
            'max_choices' => $type === 'single'
                ? 1
                : $this->input('max_choices'),
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $min = (int) $this->input('min_choices', 0);
            $max = $this->input('max_choices');

            if ($max !== null && $max !== '' && (int) $max < $min) {
                $validator->errors()->add('max_choices', 'الحد الأقصى يجب أن يكون أكبر من أو يساوي الحد الأدنى.');
            }

            if ($this->boolean('is_required') && $min < 1) {
                $validator->errors()->add('min_choices', 'المجموعة الإجبارية يجب أن يكون الحد الأدنى فيها 1 على الأقل.');
            }
        });
    }
}