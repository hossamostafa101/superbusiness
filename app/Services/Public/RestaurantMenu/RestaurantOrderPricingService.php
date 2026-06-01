<?php

namespace App\Services\Public\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantBranch;
use App\Models\RestaurantMenu\RestaurantItemOption;
use App\Models\RestaurantMenu\RestaurantItemVariant;
use App\Models\RestaurantMenu\RestaurantMenuItem;
use App\Models\RestaurantMenu\RestaurantMenuOffer;
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
            $lineType = $rawLine['line_type'] ?? (! empty($rawLine['offer_id']) ? 'offer' : 'item');

            if ($lineType === 'offer') {
                if (empty($rawLine['offer_id'])) {
                    throw new RuntimeException('العرض مطلوب.');
                }

                $offer = RestaurantMenuOffer::query()
                    ->where('workspace_id', $workspace->id)
                    ->where(function ($query) use ($branch) {
                        $query->whereNull('branch_id')
                            ->orWhere('branch_id', $branch->id);
                    })
                    ->where('is_active', true)
                    ->where('is_orderable', true)
                    ->where('id', $rawLine['offer_id'])
                    ->first();

                if (! $offer) {
                    throw new RuntimeException('العرض غير متاح حاليًا.');
                }

                $quantity = max(1, (int) ($rawLine['quantity'] ?? 1));

                $unitPrice = (float) (
                    $offer->new_price
    ?: $offer->old_price
                    ?? $offer->price
                    ?? 0
                );

                if ($unitPrice <= 0) {
                    throw new RuntimeException('سعر العرض غير صحيح.');
                }

                $lineTotal = $unitPrice * $quantity;
                $currency = $offer->currency ?: $currency;

                $lines[] = [
                    'line_type' => 'offer',
                    'offer' => $offer,
                    'item' => null,
                    'variant' => null,
                    'options' => collect(),
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'options_total' => 0,
                    'line_total' => $lineTotal,
                    'currency' => $currency,
                    'notes' => $rawLine['notes'] ?? null,
                ];

                $subtotal += $lineTotal;

                continue;
            }

            if (empty($rawLine['item_id'])) {
                throw new RuntimeException('الصنف مطلوب.');
            }

            $item = RestaurantMenuItem::query()
                ->where('workspace_id', $workspace->id)
                ->where('branch_id', $branch->id)
                ->where('is_available', true)
                ->with([
                    'activeVariants',
                    'activeOptionGroups.options',
                ])
                ->find($rawLine['item_id']);

            if (! $item) {
                throw new RuntimeException('الصنف غير متاح حاليًا.');
            }

            $variant = null;

            if (! empty($rawLine['variant_id'])) {
                $variant = RestaurantItemVariant::query()
                    ->where('workspace_id', $workspace->id)
                    ->where('branch_id', $branch->id)
                    ->where('item_id', $item->id)
                    ->where('is_active', true)
                    ->find($rawLine['variant_id']);

                if (! $variant) {
                    throw new RuntimeException("الاختيار السعري غير متاح للصنف {$item->name}.");
                }
            }

            $selectedOptionIds = collect($rawLine['options'] ?? [])
                ->filter()
                ->map(fn ($id) => (int) $id)
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

            $optionsTotal = $options->sum(fn ($option) => (float) $option->price);

            $quantity = max(1, (int) ($rawLine['quantity'] ?? 1));
            $lineTotal = ($unitPrice + $optionsTotal) * $quantity;

            $currency = $variant?->currency ?: $item->currency ?: $currency;

            $lines[] = [
                'line_type' => 'item',
                'offer' => null,
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

        if (empty($lines)) {
            throw new RuntimeException('يجب إضافة صنف أو عرض واحد على الأقل.');
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
            $groupOptionIds = $group->options
                ->pluck('id')
                ->map(fn ($id) => (int) $id);

            $selectedInGroup = $selectedOptionIds
                ->intersect($groupOptionIds)
                ->count();

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