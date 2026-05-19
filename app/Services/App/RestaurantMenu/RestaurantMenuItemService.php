<?php

namespace App\Services\App\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantMenuItem;
use App\Models\Workspace;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RestaurantMenuItemService
{
    public function create(
        Workspace $workspace,
        array $data,
        ?UploadedFile $image = null
    ): RestaurantMenuItem {
        $branch = $workspace->restaurantBranches()
            ->where('id', $data['branch_id'])
            ->firstOrFail();

        $categoryId = $this->resolveCategoryId($workspace, $branch->id, $data['category_id'] ?? null);

        if ($image) {
            $data['image'] = $image->store('restaurant-menu/items', 'public');
        }

        return $branch->items()->create([
            'workspace_id' => $workspace->id,
            'category_id' => $categoryId,

            'name' => $data['name'],
            'description' => $data['description'] ?? null,

            'price' => $data['price'] ?? 0,
            'sale_price' => $data['sale_price'] ?? null,
            'currency' => $data['currency'] ?? 'EGP',

            'image' => $data['image'] ?? null,

            'calories' => $data['calories'] ?? null,
            'preparation_time_minutes' => $data['preparation_time_minutes'] ?? null,

            'is_available' => $data['is_available'] ?? true,
            'is_featured' => $data['is_featured'] ?? false,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(
        Workspace $workspace,
        RestaurantMenuItem $item,
        array $data,
        ?UploadedFile $image = null
    ): RestaurantMenuItem {
        $branch = $workspace->restaurantBranches()
            ->where('id', $data['branch_id'])
            ->firstOrFail();

        $categoryId = $this->resolveCategoryId($workspace, $branch->id, $data['category_id'] ?? null);

        $imagePath = $item->image;

        if (! empty($data['remove_image']) && $imagePath) {
            Storage::disk('public')->delete($imagePath);
            $imagePath = null;
        }

        if ($image) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $image->store('restaurant-menu/items', 'public');
        }

        $item->update([
            'branch_id' => $branch->id,
            'category_id' => $categoryId,

            'name' => $data['name'],
            'description' => $data['description'] ?? null,

            'price' => $data['price'] ?? 0,
            'sale_price' => $data['sale_price'] ?? null,
            'currency' => $data['currency'] ?? 'EGP',

            'image' => $imagePath,

            'calories' => $data['calories'] ?? null,
            'preparation_time_minutes' => $data['preparation_time_minutes'] ?? null,

            'is_available' => $data['is_available'] ?? false,
            'is_featured' => $data['is_featured'] ?? false,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return $item;
    }

    public function delete(RestaurantMenuItem $item): void
    {
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();
    }

    private function resolveCategoryId(Workspace $workspace, int $branchId, ?int $categoryId): ?int
    {
        if (! $categoryId) {
            return null;
        }

        $category = $workspace->restaurantMenuCategories()
            ->where('id', $categoryId)
            ->where('branch_id', $branchId)
            ->first();

        return $category?->id;
    }
}