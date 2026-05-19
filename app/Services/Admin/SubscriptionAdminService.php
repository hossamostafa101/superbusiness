<?php

namespace App\Services\Admin;

use App\Models\Subscription;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class SubscriptionAdminService
{
    public function create(array $data): Subscription
    {
        return DB::transaction(function () use ($data) {
            $workspace = Workspace::findOrFail($data['workspace_id']);

            Subscription::query()
                ->where('workspace_id', $workspace->id)
                ->whereIn('status', ['trialing', 'active', 'past_due'])
                ->update([
                    'status' => 'expired',
                    'ends_at' => now(),
                ]);

            return Subscription::create([
                'workspace_id' => $workspace->id,
                'plan_id' => $data['plan_id'],
                'status' => $data['status'],
                'billing_cycle' => $data['billing_cycle'],
                'starts_at' => $data['starts_at'] ?? now(),
                'trial_ends_at' => $data['trial_ends_at'] ?? null,
                'ends_at' => $data['ends_at'] ?? $this->defaultEndsAt($data['billing_cycle']),
                'cancelled_at' => $data['status'] === 'cancelled' ? now() : null,
            ]);
        });
    }

    public function update(Subscription $subscription, array $data): Subscription
{
    return DB::transaction(function () use ($subscription, $data) {
        $endsAt = $data['ends_at'] ?? null;

        if (! $endsAt && in_array($data['status'], ['trialing', 'active'], true)) {
            $endsAt = $this->defaultEndsAt($data['billing_cycle']);
        }

        $subscription->update([
            'plan_id' => $data['plan_id'],
            'status' => $data['status'],
            'billing_cycle' => $data['billing_cycle'],
            'starts_at' => $data['starts_at'] ?? $subscription->starts_at ?? now(),
            'trial_ends_at' => $data['trial_ends_at'] ?? null,
            'ends_at' => $endsAt,
            'cancelled_at' => $data['status'] === 'cancelled'
                ? ($subscription->cancelled_at ?? now())
                : null,
        ]);

        return $subscription;
    });
}

    public function cancel(Subscription $subscription): Subscription
    {
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'ends_at' => $subscription->ends_at ?? now(),
        ]);

        return $subscription;
    }

    public function markActive(Subscription $subscription): Subscription
    {
        return DB::transaction(function () use ($subscription) {
            Subscription::query()
                ->where('workspace_id', $subscription->workspace_id)
                ->where('id', '!=', $subscription->id)
                ->whereIn('status', ['trialing', 'active', 'past_due'])
                ->update([
                    'status' => 'expired',
                    'ends_at' => now(),
                ]);

            $subscription->update([
                'status' => 'active',
                'starts_at' => $subscription->starts_at ?? now(),
                'cancelled_at' => null,
                'ends_at' => $subscription->ends_at ?? $this->defaultEndsAt($subscription->billing_cycle),
            ]);

            return $subscription;
        });
    }

    public function delete(Subscription $subscription): void
    {
        $subscription->delete();
    }

    private function defaultEndsAt(string $billingCycle)
    {
        return match ($billingCycle) {
            'yearly' => now()->addYear(),
            default => now()->addMonth(),
        };
    }
}