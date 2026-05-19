<?php

namespace App\Services\App;

use App\Models\BusinessCategory;
use App\Models\Workspace;

class BusinessCategoryService
{
    public function create(Workspace $workspace, array $data): BusinessCategory
    {
        return $workspace->businessCategories()->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update(BusinessCategory $category, array $data): BusinessCategory
    {
        $category->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? false,
        ]);

        return $category;
    }

    public function delete(BusinessCategory $category): void
    {
        $category->delete();
    }
}