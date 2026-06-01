<?php

namespace App\Http\Requests\Public\RestaurantMenu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePublicRestaurantOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
{
    $items = collect($this->input('items', []))
        ->map(function ($line) {
            if (! is_array($line)) {
                return $line;
            }

            $lineType = $line['line_type'] ?? null;

            if (! in_array($lineType, ['item', 'offer'], true)) {
                $lineType = ! empty($line['offer_id']) ? 'offer' : 'item';
            }

            $line['line_type'] = $lineType;

            return $line;
        })
        ->values()
        ->all();

    $this->merge([
        'items' => $items,
    ]);
}
    protected function prepareForValidationX(): void
    {
        $items = collect($this->input('items', []))
            ->map(function (array $line) {
                $lineType = $line['line_type'] ?? null;

                if (! in_array($lineType, ['item', 'offer'], true)) {
                    $lineType = ! empty($line['offer_id']) ? 'offer' : 'item';
                }

                $line['line_type'] = $lineType;

                return $line;
            })
            ->values()
            ->all();

        $this->merge([
            'items' => $items,
        ]);
    }

    public function rules(): array
    {
        $workspace = $this->route('workspace');
        $branch = $this->route('branch');

        return [
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'customer_email' => ['nullable', 'email', 'max:255'],

            'order_type' => [
                'required',
                Rule::in(['dine_in', 'takeaway', 'delivery']),
            ],

            'table_number' => ['nullable', 'string', 'max:50'],
            'table_code' => ['nullable', 'string', 'max:80'],
            'delivery_address' => ['nullable', 'string', 'max:3000'],
            'notes' => ['nullable', 'string', 'max:3000'],
            'invoice_id' => ['nullable', 'integer'],

            'items' => ['required', 'array', 'min:1'],

            'items.*.line_type' => [
                'required',
                Rule::in(['item', 'offer']),
            ],

            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'items.*.notes' => ['nullable', 'string', 'max:1000'],

            'items.*.item_id' => [
                'nullable',
                'integer',
                Rule::exists('restaurant_menu_items', 'id')
                    ->where('workspace_id', $workspace?->id)
                    ->where('branch_id', $branch?->id)
                    ->where('is_available', 1),
            ],

            'items.*.offer_id' => [
                'nullable',
                'integer',
                Rule::exists('restaurant_menu_offers', 'id')
                    ->where('workspace_id', $workspace?->id)
                    ->where('is_active', 1)
                    ->where('is_orderable', 1),
            ],

            'items.*.variant_id' => [
                'nullable',
                'integer',
                Rule::exists('restaurant_item_variants', 'id')
                    ->where('workspace_id', $workspace?->id)
                    ->where('branch_id', $branch?->id)
                    ->where('is_active', 1),
            ],

            'items.*.options' => ['nullable', 'array'],

            'items.*.options.*' => [
                'integer',
                Rule::exists('restaurant_item_options', 'id')
                    ->where('workspace_id', $workspace?->id)
                    ->where('branch_id', $branch?->id)
                    ->where('is_active', 1),
            ],






            'delivery_zone_id' => ['nullable', 'integer'],
            'delivery_address_details' => ['nullable', 'string', 'max:3000'],
            'delivery_area' => ['nullable', 'string', 'max:150'],
            'delivery_building' => ['nullable', 'string', 'max:100'],
            'delivery_floor' => ['nullable', 'string', 'max:100'],
            'delivery_apartment' => ['nullable', 'string', 'max:100'],
            'delivery_landmark' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // if ($this->input('order_type') === 'delivery' && ! $this->filled('delivery_address')) {
            //     $validator->errors()->add('delivery_address', 'عنوان التوصيل مطلوب لطلبات الدليفري.');
            // }
            if ($this->input('order_type') === 'delivery') {
                $workspace = $this->route('workspace');
                $branch = $this->route('branch');

                $deliverySettings = null;

                if ($workspace && $branch) {
                    $deliverySettings = app(\App\Services\App\RestaurantMenu\RestaurantDeliverySettingsService::class)
                        ->get($workspace, $branch);
                }

                if ($deliverySettings && ! $deliverySettings->is_enabled) {
                    $validator->errors()->add('order_type', 'الدليفري غير متاح حاليًا.');
                }

                if (
                    $deliverySettings
                    && $deliverySettings->require_zone_for_delivery
                    && ! $this->filled('delivery_zone_id')
                ) {
                    $validator->errors()->add('delivery_zone_id', 'منطقة التوصيل مطلوبة.');
                }

                if (
                    ! $this->filled('delivery_address')
                    && ! $this->filled('delivery_address_details')
                ) {
                    $validator->errors()->add('delivery_address_details', 'عنوان التوصيل مطلوب.');
                }
            }

            if ($this->input('order_type') === 'delivery' && $this->filled('delivery_zone_id')) {
                $workspace = $this->route('workspace');
                $branch = $this->route('branch');

                $zoneExists = \App\Models\RestaurantMenu\RestaurantDeliveryZone::query()
                    ->where('workspace_id', $workspace?->id)
                    ->where('is_active', true)
                    ->whereKey($this->input('delivery_zone_id'))
                    ->where(function ($query) use ($branch) {
                        $query->whereNull('branch_id')
                            ->orWhere('branch_id', $branch?->id);
                    })
                    ->exists();

                if (! $zoneExists) {
                    $validator->errors()->add('delivery_zone_id', 'منطقة التوصيل غير صحيحة.');
                }
            }



            if (
                $this->input('order_type') === 'dine_in'
                && ! $this->filled('table_number')
                && ! $this->filled('table_code')
            ) {
                $validator->errors()->add('table_number', 'رقم الطاولة مطلوب لطلبات داخل المكان.');
            }

            foreach ($this->input('items', []) as $index => $line) {
                $lineType = $line['line_type'] ?? 'item';

                if ($lineType === 'offer') {
                    if (empty($line['offer_id'])) {
                        $validator->errors()->add(
                            "items.$index.offer_id",
                            'العرض مطلوب.'
                        );
                    }

                    continue;
                }

                if ($lineType === 'item') {
                    if (empty($line['item_id'])) {
                        $validator->errors()->add(
                            "items.$index.item_id",
                            'الصنف مطلوب.'
                        );
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'اسم العميل مطلوب.',
            'customer_phone.required' => 'رقم الهاتف مطلوب.',
            'order_type.required' => 'نوع الطلب مطلوب.',
            'items.required' => 'يجب إضافة صنف واحد على الأقل.',
            'items.min' => 'يجب إضافة صنف واحد على الأقل.',
            'items.*.line_type.required' => 'نوع السطر مطلوب.',
            'items.*.line_type.in' => 'نوع السطر غير صحيح.',
        ];
    }
}
