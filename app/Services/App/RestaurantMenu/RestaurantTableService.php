<?php

namespace App\Services\App\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantTable;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RestaurantTableService
{
    public function create(Workspace $workspace, array $data): RestaurantTable
    {
        return DB::transaction(function () use ($workspace, $data) {
            $branch = $workspace->restaurantBranches()
                ->where('id', $data['branch_id'])
                ->firstOrFail();

            $this->ensureNumberIsUniqueInBranch(
                branchId: $branch->id,
                number: $data['number']
            );

            return $branch->tables()->create([
                'workspace_id' => $workspace->id,
                'name' => $data['name'],
                'number' => $data['number'],
                'code' => $this->generateUniqueCode($workspace),
                'seats' => $data['seats'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'sort_order' => $data['sort_order'] ?? 0,
                'metadata' => null,
            ]);
        });
    }

    public function update(Workspace $workspace, RestaurantTable $table, array $data): RestaurantTable
    {
        return DB::transaction(function () use ($workspace, $table, $data) {
            $branch = $workspace->restaurantBranches()
                ->where('id', $data['branch_id'])
                ->firstOrFail();

            $this->ensureNumberIsUniqueInBranch(
                branchId: $branch->id,
                number: $data['number'],
                ignoreTableId: $table->id
            );

            $table->update([
                'branch_id' => $branch->id,
                'name' => $data['name'],
                'number' => $data['number'],
                'seats' => $data['seats'] ?? null,
                'is_active' => $data['is_active'] ?? false,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            return $table;
        });
    }

    public function regenerateCode(Workspace $workspace, RestaurantTable $table): RestaurantTable
    {
        $table->update([
            'code' => $this->generateUniqueCode($workspace),
        ]);

        return $table;
    }

    public function delete(RestaurantTable $table): void
    {
        $table->delete();
    }

    private function ensureNumberIsUniqueInBranch(int $branchId, string $number, ?int $ignoreTableId = null): void
    {
        $exists = RestaurantTable::query()
            ->where('branch_id', $branchId)
            ->where('number', $number)
            ->when($ignoreTableId, fn ($query) => $query->where('id', '!=', $ignoreTableId))
            ->exists();

        if ($exists) {
            throw new \RuntimeException('رقم الطاولة مستخدم بالفعل داخل هذا الفرع.');
        }
    }

    private function generateUniqueCode(Workspace $workspace): string
    {
        do {
            $code = 'tbl_' . Str::lower(Str::random(16));
        } while (
            RestaurantTable::query()
                ->where('workspace_id', $workspace->id)
                ->where('code', $code)
                ->exists()
        );

        return $code;
    }
}