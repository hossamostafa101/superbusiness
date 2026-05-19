<?php

namespace App\Services\App\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantItemVariant;
use App\Models\RestaurantMenu\RestaurantMenuItem;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class RestaurantItemVariantService
{
    public function create(
        Workspace $workspace,
        RestaurantMenuItem $item,
        array $data
    ): RestaurantItemVariant {
        return DB::transaction(function () use ($workspace, $item, $data) {
            if (! empty($data['is_default'])) {
                $this->clearDefaultVariants($item);
            }

            $variant = $item->variants()->create([
                'workspace_id' => $workspace->id,
                'branch_id' => $item->branch_id,

                'name' => $data['name'],

                'price' => $data['price'] ?? 0,
                'sale_price' => $data['sale_price'] ?? null,
                'currency' => $data['currency'] ?? $item->currency ?? 'EGP',

                'is_default' => $data['is_default'] ?? false,
                'is_active' => $data['is_active'] ?? true,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            $this->ensureDefaultVariant($item, $variant);

            return $variant;
        });
    }

    public function update(
        RestaurantMenuItem $item,
        RestaurantItemVariant $variant,
        array $data
    ): RestaurantItemVariant {
        return DB::transaction(function () use ($item, $variant, $data) {
            if (! empty($data['is_default'])) {
                $this->clearDefaultVariants($item, $variant->id);
            }

            $variant->update([
                'name' => $data['name'],

                'price' => $data['price'] ?? 0,
                'sale_price' => $data['sale_price'] ?? null,
                'currency' => $data['currency'] ?? $variant->currency ?? 'EGP',

                'is_default' => $data['is_default'] ?? false,
                'is_active' => $data['is_active'] ?? false,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            $this->ensureDefaultVariant($item, $variant);

            return $variant;
        });
    }

    public function delete(RestaurantMenuItem $item, RestaurantItemVariant $variant): void
    {
        DB::transaction(function () use ($item, $variant) {
            $wasDefault = $variant->is_default;

            $variant->delete();

            if ($wasDefault) {
                $nextVariant = $item->variants()
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->first();

                if ($nextVariant) {
                    $nextVariant->update([
                        'is_default' => true,
                    ]);
                }
            }
        });
    }

    private function clearDefaultVariants(RestaurantMenuItem $item, ?int $exceptVariantId = null): void
    {
        $item->variants()
            ->when($exceptVariantId, fn ($query) => $query->where('id', '!=', $exceptVariantId))
            ->update([
                'is_default' => false,
            ]);
    }

    private function ensureDefaultVariant(RestaurantMenuItem $item, RestaurantItemVariant $variant): void
    {
        $hasDefault = $item->variants()
            ->where('is_default', true)
            ->exists();

        if (! $hasDefault) {
            $variant->update([
                'is_default' => true,
            ]);
        }
    }
}