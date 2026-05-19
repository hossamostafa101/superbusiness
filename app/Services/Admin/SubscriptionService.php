<?php

namespace App\Services\Admin;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function createOrReplaceSubscription(
        Workspace $workspace,
        Plan $plan,
        string $billingCycle = 'monthly',
        string $status = 'active'
    ): Subscription {
        return DB::transaction(function () use ($workspace, $plan, $billingCycle, $status) {
            Subscription::query()
                ->where('workspace_id', $workspace->id)
                ->whereIn('status', ['trialing', 'active', 'past_due'])
                ->update([
                    'status' => 'expired',
                    'ends_at' => now(),
                    'updated_at' => now(),
                ]);

            return Subscription::create([
                'workspace_id' => $workspace->id,
                'plan_id' => $plan->id,
                'status' => $status,
                'billing_cycle' => $billingCycle,
                'starts_at' => now(),
                'trial_ends_at' => null,
                'ends_at' => $this->calculateEndsAt($billingCycle),
            ]);
        });
    }

    public function activateFromPayment(Payment $payment): Subscription
    {
        return DB::transaction(function () use ($payment) {
            $payment->loadMissing(['workspace', 'plan']);

            if (! $payment->workspace || ! $payment->plan) {
                throw new \RuntimeException('بيانات الدفع غير مكتملة.');
            }

            $subscription = $this->createOrReplaceSubscription(
                workspace: $payment->workspace,
                plan: $payment->plan,
                billingCycle: $payment->billing_cycle ?? 'monthly',
                status: 'active'
            );

            $payment->update([
                'subscription_id' => $subscription->id,
                'status' => $payment->provider === 'manual' ? 'approved' : 'paid',
                'paid_at' => $payment->paid_at ?? now(),
            ]);

            return $subscription;
        });
    }

    private function calculateEndsAt(string $billingCycle)
    {
        return match ($billingCycle) {
            'yearly' => now()->addYear(),
            default => now()->addMonth(),
        };
    }
}