<?php

namespace App\Services\Admin;

use App\Models\Plan;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class WorkspaceService
{
    public function create(array $data): Workspace
    {
        return DB::transaction(function () use ($data) {
            $workspace = Workspace::create([
                'owner_id' => $data['owner_id'],
                'name' => $data['name'],
                'slug' => $data['slug'],
                'type' => $data['type'],
                'status' => $data['status'],
                'trial_ends_at' => $data['trial_ends_at'] ?? null,
            ]);

            $workspace->users()->syncWithoutDetaching([
                $data['owner_id'] => [
                    'role' => 'owner',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            $this->createDefaultSubscription($workspace);

            return $workspace;
        });
    }

    public function update(Workspace $workspace, array $data): Workspace
    {
        return DB::transaction(function () use ($workspace, $data) {
            $oldOwnerId = $workspace->owner_id;

            $workspace->update([
                'owner_id' => $data['owner_id'],
                'name' => $data['name'],
                'slug' => $data['slug'],
                'type' => $data['type'],
                'status' => $data['status'],
                'trial_ends_at' => $data['trial_ends_at'] ?? null,
            ]);

            if ((int) $oldOwnerId !== (int) $data['owner_id']) {
                $workspace->users()->syncWithoutDetaching([
                    $data['owner_id'] => [
                        'role' => 'owner',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            }

            return $workspace;
        });
    }

    public function toggleStatus(Workspace $workspace): Workspace
    {
        $workspace->update([
            'status' => $workspace->status === 'active' ? 'suspended' : 'active',
        ]);

        return $workspace;
    }

    public function delete(Workspace $workspace): void
    {
        DB::transaction(function () use ($workspace) {
            $workspace->users()->detach();
            $workspace->delete();
        });
    }

    private function createDefaultSubscription(Workspace $workspace): void
    {
        $freePlan = Plan::query()
            ->where('slug', 'free')
            ->where('is_active', true)
            ->first();

        if (! $freePlan) {
            return;
        }

        $workspace->subscriptions()->create([
            'plan_id' => $freePlan->id,
            'status' => 'trialing',
            'billing_cycle' => 'monthly',
            'starts_at' => now(),
            'trial_ends_at' => now()->addDays(14),
            'ends_at' => null,
        ]);
    }
}