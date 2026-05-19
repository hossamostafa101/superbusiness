<?php

namespace App\Services\Admin;

use App\Models\Feature;

class FeatureService
{
    public function create(array $data): Feature
    {
        return Feature::create([
            'name' => $data['name'],
            'key' => $data['key'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'module' => $data['module'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);
    }

    public function update(Feature $feature, array $data): Feature
    {
        $feature->update([
            'name' => $data['name'],
            'key' => $data['key'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'module' => $data['module'] ?? null,
            'is_active' => $data['is_active'] ?? false,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return $feature;
    }

    public function toggleStatus(Feature $feature): Feature
    {
        $feature->update([
            'is_active' => ! $feature->is_active,
        ]);

        return $feature;
    }

    public function delete(Feature $feature): void
    {
        $feature->delete();
    }
}