<?php

namespace App\Services\App\RestaurantMenu;

use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class RestaurantMenuSettingsService
{
    public function values(Workspace $workspace): array
    {
        $settings = $workspace->restaurantMenuSettings()
            ->whereNull('branch_id')
            ->pluck('value', 'key')
            ->toArray();

        return [
            'restaurant_ordering_mode' => $settings['restaurant_ordering_mode'] ?? 'single_order',
            'restaurant_invoice_duration_minutes' => (int) ($settings['restaurant_invoice_duration_minutes'] ?? 120),
            'restaurant_invoice_join_policy' => $settings['restaurant_invoice_join_policy'] ?? 'allow_with_pin',
            'restaurant_allow_new_invoice_when_table_busy' => (string) ($settings['restaurant_allow_new_invoice_when_table_busy'] ?? '1'),
            'restaurant_invoice_extend_minutes_step' => (int) ($settings['restaurant_invoice_extend_minutes_step'] ?? 30),
        ];
    }

    public function update(Workspace $workspace, array $data): void
    {
        DB::transaction(function () use ($workspace, $data) {
            $payload = [
                'restaurant_ordering_mode' => $data['restaurant_ordering_mode'],
                'restaurant_invoice_duration_minutes' => (string) $data['restaurant_invoice_duration_minutes'],
                'restaurant_invoice_join_policy' => $data['restaurant_invoice_join_policy'],
                'restaurant_allow_new_invoice_when_table_busy' => ! empty($data['restaurant_allow_new_invoice_when_table_busy']) ? '1' : '0',
                'restaurant_invoice_extend_minutes_step' => (string) $data['restaurant_invoice_extend_minutes_step'],
            ];

            foreach ($payload as $key => $value) {
                $workspace->restaurantMenuSettings()->updateOrCreate(
                    [
                        'branch_id' => null,
                        'key' => $key,
                    ],
                    [
                        'value' => $value,
                    ]
                );
            }
        });
    }
}