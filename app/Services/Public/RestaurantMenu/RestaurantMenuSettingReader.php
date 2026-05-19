<?php

namespace App\Services\Public\RestaurantMenu;

use App\Models\Workspace;

class RestaurantMenuSettingReader
{
    public function get(Workspace $workspace, string $key, mixed $default = null): mixed
    {
        $setting = $workspace->restaurantMenuSettings()
            ->whereNull('branch_id')
            ->where('key', $key)
            ->first();

        return $setting?->value ?? $default;
    }

    public function orderingMode(Workspace $workspace): string
    {
        return (string) $this->get($workspace, 'restaurant_ordering_mode', 'single_order');
    }

    public function invoiceDurationMinutes(Workspace $workspace): int
    {
        return max(15, (int) $this->get($workspace, 'restaurant_invoice_duration_minutes', 120));
    }

    public function joinPolicy(Workspace $workspace): string
    {
        return (string) $this->get($workspace, 'restaurant_invoice_join_policy', 'allow_with_pin');
    }

    public function allowNewInvoiceWhenTableBusy(Workspace $workspace): bool
    {
        return in_array((string) $this->get($workspace, 'restaurant_allow_new_invoice_when_table_busy', '1'), ['1', 'true', 'yes'], true);
    }

    public function extendMinutesStep(Workspace $workspace): int
    {
        return max(5, (int) $this->get($workspace, 'restaurant_invoice_extend_minutes_step', 30));
    }
}