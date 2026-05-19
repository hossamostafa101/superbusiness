<?php

namespace App\Services\Core;

use App\Models\Workspace;

class FeatureLimitService
{
    public function value(Workspace $workspace, string $featureKey, mixed $default = null): mixed
    {
        $workspace->loadMissing('activeSubscription.plan.features');

        $subscription = $workspace->activeSubscription;

        if (! $subscription || ! $subscription->plan) {
            return $default;
        }

        $feature = $subscription->plan->features
            ->firstWhere('key', $featureKey);

        if (! $feature) {
            return $default;
        }

        return $feature->pivot->value ?? $default;
    }

    public function limit(Workspace $workspace, string $featureKey, int $default = 0): int
    {
        return (int) $this->value($workspace, $featureKey, $default);
    }

    public function enabled(Workspace $workspace, string $featureKey): bool
    {
        $value = $this->value($workspace, $featureKey, false);

        return in_array((string) $value, ['1', 'true', 'yes', 'on'], true);
    }

    public function canCreate(Workspace $workspace, string $featureKey, int $currentCount): bool
    {
        $limit = $this->limit($workspace, $featureKey, 0);

        if ($limit === -1) {
            return true;
        }

        return $currentCount < $limit;
    }
}