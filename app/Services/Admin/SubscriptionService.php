<?php

namespace App\Services\Admin;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Modules\Affiliate\Services\AffiliateCommissionService;

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

        $workspace = $payment->workspace;
        $plan = $payment->plan;

        $subscription = $this->createOrReplaceSubscription(
            workspace: $workspace,
            plan: $plan,
            billingCycle: $payment->billing_cycle ?? 'monthly',
            status: 'active'
        );

        $payment->update([
            'subscription_id' => $subscription->id,
            'status' => $payment->provider === 'manual' ? 'approved' : 'paid',
            'paid_at' => $payment->paid_at ?? now(),
        ]);

        $payment->refresh();

        /*
         * إنشاء عمولة الأفلييت فقط بعد نجاح الدفع وتفعيل الاشتراك.
         * الخدمة نفسها تمنع تكرار العمولة لنفس payment_id.
         */
        app(AffiliateCommissionService::class)
            ->createForPaidSubscription([
                'workspace_id' => $workspace->id,

                'referred_user_id' => $workspace->user_id
                    ?? $workspace->owner_id
                    ?? auth()->id(),

                'subscription_id' => $subscription->id,
                'payment_id' => $payment->id,
                'plan_id' => $plan->id,

                'amount' => $payment->amount ?? 0,
                'currency' => $payment->currency ?? 'EGP',

                'type' => 'subscription_payment',
                'paid_at' => $payment->paid_at ?? now(),

                'notes' => 'Affiliate commission for paid subscription',
            ]);

        return $subscription;
    });
}
    public function activateFromPaymentX(Payment $payment): Subscription
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


            app(\Modules\Affiliate\Services\AffiliateCommissionService::class)
    ->createForPaidSubscription([
        'workspace_id' => $payment->workspace,
        'referred_user_id' => $workspace->user_id ?? auth()->id(),

        'subscription_id' => $subscription->id ?? null,
        'payment_id' => $payment->id ?? null,
        'plan_id' => $payment->plan->id ?? null,

        'amount' => $payment->amount ?? $plan->price ?? 0,
        'currency' => $payment->currency ?? 'EGP',

        'type' => 'subscription_payment',
        'paid_at' => $payment->paid_at ?? now(),

        'notes' => 'Affiliate commission for paid subscription',
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