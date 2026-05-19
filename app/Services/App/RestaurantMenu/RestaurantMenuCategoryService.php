<?php

namespace App\Services\App\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantMenuCategory;
use App\Models\Workspace;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RestaurantMenuCategoryService
{
    public function create(
        Workspace $workspace,
        array $data,
        ?UploadedFile $image = null
    ): RestaurantMenuCategory {
        $branch = $workspace->restaurantBranches()
            ->where('id', $data['branch_id'])
            ->firstOrFail();

        if ($image) {
            $data['image'] = $image->store('restaurant-menu/categories', 'public');
        }

        return $branch->categories()->create([
            'workspace_id' => $workspace->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update(
        Workspace $workspace,
        RestaurantMenuCategory $category,
        array $data,
        ?UploadedFile $image = null
    ): RestaurantMenuCategory {
        $branch = $workspace->restaurantBranches()
            ->where('id', $data['branch_id'])
            ->firstOrFail();

        $imagePath = $category->image;

        if (! empty($data['remove_image']) && $imagePath) {
            Storage::disk('public')->delete($imagePath);
            $imagePath = null;
        }

        if ($image) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $image->store('restaurant-menu/categories', 'public');
        }

        $category->update([
            'branch_id' => $branch->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'image' => $imagePath,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? false,
        ]);

        return $category;
    }

    public function delete(RestaurantMenuCategory $category): void
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();
    }
}