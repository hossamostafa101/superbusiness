<?php

namespace App\Services\App;

use App\Models\BusinessService;
use App\Models\Workspace;

class BusinessServiceService
{
    public function create(Workspace $workspace, array $data): BusinessService
    {
        return $workspace->businessServices()->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'duration_minutes' => $data['duration_minutes'] ?? 30,
            'price' => $data['price'] ?? null,
            'currency' => $data['currency'] ?? 'EGP',
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public function update(BusinessService $service, array $data): BusinessService
    {
        $service->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'duration_minutes' => $data['duration_minutes'] ?? 30,
            'price' => $data['price'] ?? null,
            'currency' => $data['currency'] ?? 'EGP',
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? false,
        ]);

        return $service;
    }

    public function delete(BusinessService $service): void
    {
        $service->delete();
    }
}