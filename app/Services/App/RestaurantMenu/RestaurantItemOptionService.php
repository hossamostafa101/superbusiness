<?php

namespace App\Services\App\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantItemOption;
use App\Models\RestaurantMenu\RestaurantItemOptionGroup;
use App\Models\RestaurantMenu\RestaurantMenuItem;
use App\Models\Workspace;

class RestaurantItemOptionService
{
    public function create(
        Workspace $workspace,
        RestaurantMenuItem $item,
        RestaurantItemOptionGroup $group,
        array $data
    ): RestaurantItemOption {
        return $group->options()->create([
            'workspace_id' => $workspace->id,
            'branch_id' => $item->branch_id,
            'item_id' => $item->id,

            'name' => $data['name'],

            'price' => $data['price'] ?? 0,
            'currency' => $data['currency'] ?? $item->currency ?? 'EGP',

            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(
        RestaurantItemOption $option,
        array $data
    ): RestaurantItemOption {
        $option->update([
            'name' => $data['name'],

            'price' => $data['price'] ?? 0,
            'currency' => $data['currency'] ?? $option->currency ?? 'EGP',

            'is_active' => $data['is_active'] ?? false,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return $option;
    }

    public function delete(RestaurantItemOption $option): void
    {
        $option->delete();
    }
}