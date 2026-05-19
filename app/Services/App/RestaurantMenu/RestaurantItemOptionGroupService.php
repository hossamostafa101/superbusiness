<?php

namespace App\Services\App\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantItemOptionGroup;
use App\Models\RestaurantMenu\RestaurantMenuItem;
use App\Models\Workspace;

class RestaurantItemOptionGroupService
{
    public function create(
        Workspace $workspace,
        RestaurantMenuItem $item,
        array $data
    ): RestaurantItemOptionGroup {
        return $item->optionGroups()->create([
            'workspace_id' => $workspace->id,
            'branch_id' => $item->branch_id,

            'name' => $data['name'],
            'type' => $data['type'] ?? 'multiple',

            'is_required' => $data['is_required'] ?? false,
            'min_choices' => $data['min_choices'] ?? 0,
            'max_choices' => $data['max_choices'] ?? null,

            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update(
        RestaurantItemOptionGroup $group,
        array $data
    ): RestaurantItemOptionGroup {
        $group->update([
            'name' => $data['name'],
            'type' => $data['type'] ?? 'multiple',

            'is_required' => $data['is_required'] ?? false,
            'min_choices' => $data['min_choices'] ?? 0,
            'max_choices' => $data['max_choices'] ?? null,

            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? false,
        ]);

        return $group;
    }

    public function delete(RestaurantItemOptionGroup $group): void
    {
        $group->delete();
    }
}