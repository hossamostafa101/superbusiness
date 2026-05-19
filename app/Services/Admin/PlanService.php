<?php

namespace App\Services\Admin;

use App\Models\Plan;
use Illuminate\Support\Facades\DB;

class PlanService
{
    public function create(array $data): Plan
    {
        return DB::transaction(function () use ($data) {
            $plan = Plan::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'monthly_price' => $data['monthly_price'],
                'yearly_price' => $data['yearly_price'] ?? null,
                'currency' => $data['currency'] ?? 'EGP',
                'is_free' => $data['is_free'] ?? false,
                'is_active' => $data['is_active'] ?? true,
                'is_featured' => $data['is_featured'] ?? false,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            $this->syncFeatures($plan, $data['features'] ?? []);

            return $plan;
        });
    }

    public function update(Plan $plan, array $data): Plan
    {
        return DB::transaction(function () use ($plan, $data) {
            $plan->update([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'monthly_price' => $data['monthly_price'],
                'yearly_price' => $data['yearly_price'] ?? null,
                'currency' => $data['currency'] ?? 'EGP',
                'is_free' => $data['is_free'] ?? false,
                'is_active' => $data['is_active'] ?? false,
                'is_featured' => $data['is_featured'] ?? false,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            $this->syncFeatures($plan, $data['features'] ?? []);

            return $plan;
        });
    }

    public function toggleStatus(Plan $plan): Plan
    {
        $plan->update([
            'is_active' => ! $plan->is_active,
        ]);

        return $plan;
    }

    public function delete(Plan $plan): void
    {
        DB::transaction(function () use ($plan) {
            $plan->features()->detach();
            $plan->delete();
        });
    }

    private function syncFeatures(Plan $plan, array $features): void
    {
        $syncData = [];

        foreach ($features as $featureId => $value) {
            if ($value === null) {
                $value = '';
            }

            $syncData[$featureId] = [
                'value' => (string) $value,
            ];
        }

        $plan->features()->sync($syncData);
    }
}