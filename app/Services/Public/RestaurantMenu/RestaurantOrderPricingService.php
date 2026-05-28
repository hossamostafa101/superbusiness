<?php

namespace App\Services\Public\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantItemOption;
use App\Models\RestaurantMenu\RestaurantItemOptionGroup;
use App\Models\RestaurantMenu\RestaurantItemVariant;
use App\Models\RestaurantMenu\RestaurantMenuItem;
use App\Models\RestaurantMenu\RestaurantBranch;
use App\Models\Workspace;
use Illuminate\Support\Collection;
use RuntimeException;

class RestaurantOrderPricingService
{
    public function buildPricingPayload(
        Workspace $workspace,
        RestaurantBranch $branch,
        array $items
    ): array {
        $lines = [];
        $subtotal = 0;
        $currency = 'EGP';

        foreach ($items as $rawLine) {




            $lineType = $itemPayload['line_type'] ?? 'item';

            if ($lineType === 'offer') {
                $offer = \App\Models\RestaurantMenu\RestaurantMenuOffer::query()
                    ->where('workspace_id', $workspace->id)
                    ->where(function ($query) use ($branch) {
                        $query->whereNull('branch_id')
                            ->orWhere('branch_id', $branch->id);
                    })
                    ->where('is_active', true)
                    ->where('is_orderable', true)
                    ->find($itemPayload['offer_id'] ?? null);

                if (! $offer) {
                    continue;
                }

                $quantity = max(1, (int) ($itemPayload['quantity'] ?? 1));

                $unitPrice = (float) ($offer->new_price ?? $offer->old_price ?? 0);

                if ($unitPrice <= 0) {
                    continue;
                }

                $currency = $offer->currency ?: 'EGP';

                $lines[] = [
                    'line_type' => 'offer',
                    'offer' => $offer,
                    'item' => null,
                    'variant' => null,
                    'options' => collect(),
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'options_total' => 0,
                    'line_total' => $unitPrice * $quantity,
                    'currency' => $currency,
                    'notes' => $itemPayload['notes'] ?? null,

                    'line_type' => 'item',
                ];

                $subtotal += $unitPrice * $quantity;

                continue;
            }











            $item = RestaurantMenuItem::query()
                ->where('workspace_id', $workspace->id)
                ->where('branch_id', $branch->id)
                ->where('is_available', true)
                ->with([
                    'activeVariants',
                    'activeOptionGroups.options',
                ])
                ->findOrFail($rawLine['item_id']);

            $variant = null;

            if (! empty($rawLine['variant_id'])) {
                $variant = RestaurantItemVariant::query()
                    ->where('workspace_id', $workspace->id)
                    ->where('branch_id', $branch->id)
                    ->where('item_id', $item->id)
                    ->where('is_active', true)
                    ->findOrFail($rawLine['variant_id']);
            }

            $selectedOptionIds = collect($rawLine['options'] ?? [])
                ->filter()
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            $this->validateRequiredGroups(
                item: $item,
                selectedOptionIds: $selectedOptionIds
            );

            $options = $this->resolveOptions(
                workspace: $workspace,
                branch: $branch,
                item: $item,
                selectedOptionIds: $selectedOptionIds
            );

            $unitPrice = $variant
                ? (float) ($variant->sale_price ?? $variant->price)
                : (float) ($item->sale_price ?? $item->price);

            $optionsTotal = $options->sum(fn($option) => (float) $option->price);

            $quantity = max(1, (int) ($rawLine['quantity'] ?? 1));
            $lineTotal = ($unitPrice + $optionsTotal) * $quantity;

            $currency = $variant?->currency ?: $item->currency ?: 'EGP';

            $lines[] = [
                'item' => $item,
                'variant' => $variant,
                'options' => $options,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'options_total' => $optionsTotal,
                'line_total' => $lineTotal,
                'currency' => $currency,
                'notes' => $rawLine['notes'] ?? null,
            ];

            $subtotal += $lineTotal;
        }

        return [
            'lines' => $lines,
            'subtotal' => $subtotal,
            'discount_total' => 0,
            'delivery_fee' => 0,
            'tax_total' => 0,
            'total' => $subtotal,
            'currency' => $currency,
        ];
    }

    private function resolveOptions(
        Workspace $workspace,
        RestaurantBranch $branch,
        RestaurantMenuItem $item,
        Collection $selectedOptionIds
    ): Collection {
        if ($selectedOptionIds->isEmpty()) {
            return collect();
        }

        return RestaurantItemOption::query()
            ->where('workspace_id', $workspace->id)
            ->where('branch_id', $branch->id)
            ->where('item_id', $item->id)
            ->where('is_active', true)
            ->whereIn('id', $selectedOptionIds)
            ->with('group')
            ->get();
    }

    private function validateRequiredGroups(RestaurantMenuItem $item, Collection $selectedOptionIds): void
    {
        $groups = $item->activeOptionGroups;

        foreach ($groups as $group) {
            $groupOptionIds = $group->options->pluck('id')->map(fn($id) => (int) $id);
            $selectedInGroup = $selectedOptionIds->intersect($groupOptionIds)->count();

            if ($group->is_required && $selectedInGroup < max(1, (int) $group->min_choices)) {
                throw new RuntimeException("يجب اختيار {$group->name} للصنف {$item->name}.");
            }

            if ($group->max_choices && $selectedInGroup > (int) $group->max_choices) {
                throw new RuntimeException("تجاوزت الحد الأقصى للاختيارات في {$group->name} للصنف {$item->name}.");
            }

            if ($group->type === 'single' && $selectedInGroup > 1) {
                throw new RuntimeException("يمكن اختيار خيار واحد فقط من {$group->name} للصنف {$item->name}.");
            }
        }
    }
}
