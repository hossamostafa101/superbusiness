<?php

namespace App\Services\App\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantBranch;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class CloneRestaurantBranchService
{
    public function cloneMenuData(
        Workspace $workspace,
        RestaurantBranch $sourceBranch,
        RestaurantBranch $targetBranch
    ): void {
        DB::transaction(function () use ($workspace, $sourceBranch, $targetBranch) {
            $categoryMap = [];
            $itemMap = [];
            $groupMap = [];

            $sourceCategories = $sourceBranch->categories()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            foreach ($sourceCategories as $sourceCategory) {
                $newCategory = $targetBranch->categories()->create([
                    'workspace_id' => $workspace->id,
                    'name' => $sourceCategory->name,
                    'description' => $sourceCategory->description,
                    'image' => $sourceCategory->image,
                    'sort_order' => $sourceCategory->sort_order,
                    'is_active' => $sourceCategory->is_active,
                ]);

                $categoryMap[$sourceCategory->id] = $newCategory->id;
            }

            $sourceItems = $sourceBranch->items()
                ->with(['variants', 'optionGroups.options'])
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            foreach ($sourceItems as $sourceItem) {
                $newItem = $targetBranch->items()->create([
                    'workspace_id' => $workspace->id,
                    'category_id' => $sourceItem->category_id
                        ? ($categoryMap[$sourceItem->category_id] ?? null)
                        : null,

                    'name' => $sourceItem->name,
                    'description' => $sourceItem->description,
                    'price' => $sourceItem->price,
                    'sale_price' => $sourceItem->sale_price,
                    'currency' => $sourceItem->currency,
                    'image' => $sourceItem->image,
                    'calories' => $sourceItem->calories,
                    'preparation_time_minutes' => $sourceItem->preparation_time_minutes,
                    'is_available' => $sourceItem->is_available,
                    'is_featured' => $sourceItem->is_featured,
                    'sort_order' => $sourceItem->sort_order,
                ]);

                $itemMap[$sourceItem->id] = $newItem->id;

                foreach ($sourceItem->variants as $variant) {
                    $newItem->variants()->create([
                        'workspace_id' => $workspace->id,
                        'branch_id' => $targetBranch->id,
                        'name' => $variant->name,
                        'price' => $variant->price,
                        'sale_price' => $variant->sale_price,
                        'currency' => $variant->currency,
                        'is_default' => $variant->is_default,
                        'is_active' => $variant->is_active,
                        'sort_order' => $variant->sort_order,
                    ]);
                }

                foreach ($sourceItem->optionGroups as $group) {
                    $newGroup = $newItem->optionGroups()->create([
                        'workspace_id' => $workspace->id,
                        'branch_id' => $targetBranch->id,
                        'name' => $group->name,
                        'type' => $group->type,
                        'is_required' => $group->is_required,
                        'min_choices' => $group->min_choices,
                        'max_choices' => $group->max_choices,
                        'sort_order' => $group->sort_order,
                        'is_active' => $group->is_active,
                    ]);

                    $groupMap[$group->id] = $newGroup->id;

                    foreach ($group->options as $option) {
                        $newGroup->options()->create([
                            'workspace_id' => $workspace->id,
                            'branch_id' => $targetBranch->id,
                            'item_id' => $newItem->id,
                            'name' => $option->name,
                            'price' => $option->price,
                            'currency' => $option->currency,
                            'is_active' => $option->is_active,
                            'sort_order' => $option->sort_order,
                        ]);
                    }
                }
            }
        });
    }
}