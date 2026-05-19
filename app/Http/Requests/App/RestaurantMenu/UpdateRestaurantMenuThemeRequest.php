<?php

namespace App\Http\Requests\App\RestaurantMenu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRestaurantMenuThemeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('web')->check();
    }

    public function rules(): array
    {
        return [
            'mode' => [
                'required',
                Rule::in(['template', 'custom']),
            ],

            'template_id' => [
                'nullable',
                'integer',
                'exists:restaurant_menu_templates,id',
            ],

            'hero_section_id' => [
                'nullable',
                'integer',
                'exists:restaurant_menu_template_sections,id',
            ],

            'branch_switch_section_id' => [
                'nullable',
                'integer',
                'exists:restaurant_menu_template_sections,id',
            ],

            'category_tabs_section_id' => [
                'nullable',
                'integer',
                'exists:restaurant_menu_template_sections,id',
            ],

            'items_section_id' => [
                'nullable',
                'integer',
                'exists:restaurant_menu_template_sections,id',
            ],

            'item_modal_section_id' => [
                'nullable',
                'integer',
                'exists:restaurant_menu_template_sections,id',
            ],

            'cart_section_id' => [
                'nullable',
                'integer',
                'exists:restaurant_menu_template_sections,id',
            ],

            'invoice_section_id' => [
                'nullable',
                'integer',
                'exists:restaurant_menu_template_sections,id',
            ],

            'footer_section_id' => [
                'nullable',
                'integer',
                'exists:restaurant_menu_template_sections,id',
            ],

            'theme_color' => [
                'nullable',
                'string',
                'max:20',
            ],

            'button_color' => [
                'nullable',
                'string',
                'max:20',
            ],

            'background_color' => [
                'nullable',
                'string',
                'max:20',
            ],

            'text_color' => [
                'nullable',
                'string',
                'max:20',
            ],

            'font_family' => [
                'nullable',
                'string',
                'max:80',
            ],

            'custom_css' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'mode.required' => 'اختر طريقة التصميم.',
            'mode.in' => 'طريقة التصميم غير صحيحة.',
            'template_id.exists' => 'القالب المحدد غير موجود.',
        ];
    }
}