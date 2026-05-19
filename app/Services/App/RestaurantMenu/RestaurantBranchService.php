<?php

namespace App\Services\App\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantBranch;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RestaurantBranchService
{
    public function __construct(
        private readonly CloneRestaurantBranchService $cloneRestaurantBranchService
    ) {}

    public function create(Workspace $workspace, array $data): RestaurantBranch
    {
        return DB::transaction(function () use ($workspace, $data) {
            $slug = $this->makeUniqueSlug(
                workspace: $workspace,
                value: $data['slug'] ?? $data['name']
            );

            if (! empty($data['is_default'])) {
                $this->clearDefaultBranch($workspace);
            }

            $branch = $workspace->restaurantBranches()->create([
                'name' => $data['name'],
                'slug' => $slug,
                'phone' => $data['phone'] ?? null,
                'whatsapp_number' => $data['whatsapp_number'] ?? null,
                'address' => $data['address'] ?? null,
                'location_url' => $data['location_url'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'is_default' => $data['is_default'] ?? false,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            $this->ensureAtLeastOneDefault($workspace, $branch);

            if (! empty($data['clone_from_branch_id'])) {
                $sourceBranch = $workspace->restaurantBranches()
                    ->where('id', $data['clone_from_branch_id'])
                    ->first();

                if ($sourceBranch) {
                    $this->cloneRestaurantBranchService->cloneMenuData(
                        workspace: $workspace,
                        sourceBranch: $sourceBranch,
                        targetBranch: $branch
                    );
                }
            }

            return $branch;
        });
    }

    public function update(Workspace $workspace, RestaurantBranch $branch, array $data): RestaurantBranch
    {
        return DB::transaction(function () use ($workspace, $branch, $data) {
            $slug = $data['slug'] ?? $branch->slug;

            if ($slug !== $branch->slug) {
                $slug = $this->makeUniqueSlug(
                    workspace: $workspace,
                    value: $slug,
                    ignoreBranchId: $branch->id
                );
            }

            if (! empty($data['is_default'])) {
                $this->clearDefaultBranch($workspace, $branch->id);
            }

            $branch->update([
                'name' => $data['name'],
                'slug' => $slug,
                'phone' => $data['phone'] ?? null,
                'whatsapp_number' => $data['whatsapp_number'] ?? null,
                'address' => $data['address'] ?? null,
                'location_url' => $data['location_url'] ?? null,
                'is_active' => $data['is_active'] ?? false,
                'is_default' => $data['is_default'] ?? false,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            $this->ensureAtLeastOneDefault($workspace, $branch);

            return $branch;
        });
    }

    public function delete(Workspace $workspace, RestaurantBranch $branch): void
    {
        DB::transaction(function () use ($workspace, $branch) {
            $wasDefault = $branch->is_default;

            $branch->delete();

            if ($wasDefault) {
                $nextBranch = $workspace->restaurantBranches()
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->first();

                if ($nextBranch) {
                    $nextBranch->update([
                        'is_default' => true,
                    ]);
                }
            }
        });
    }

    private function makeUniqueSlug(Workspace $workspace, string $value, ?int $ignoreBranchId = null): string
    {
        $slug = Str::slug($value);

        if (! $slug) {
            $slug = 'branch-' . Str::random(6);
        }

        $originalSlug = $slug;
        $counter = 1;

        while (
            RestaurantBranch::query()
                ->where('workspace_id', $workspace->id)
                ->where('slug', $slug)
                ->when($ignoreBranchId, fn ($query) => $query->where('id', '!=', $ignoreBranchId))
                ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function clearDefaultBranch(Workspace $workspace, ?int $exceptBranchId = null): void
    {
        $workspace->restaurantBranches()
            ->when($exceptBranchId, fn ($query) => $query->where('id', '!=', $exceptBranchId))
            ->update([
                'is_default' => false,
            ]);
    }

    private function ensureAtLeastOneDefault(Workspace $workspace, RestaurantBranch $branch): void
    {
        $hasDefault = $workspace->restaurantBranches()
            ->where('is_default', true)
            ->exists();

        if (! $hasDefault) {
            $branch->update([
                'is_default' => true,
            ]);
        }
    }
}