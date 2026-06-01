<?php

namespace Modules\Affiliate\Services;

use App\Models\Plan;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Affiliate\Models\AffiliateCommission;
use Modules\Affiliate\Models\AffiliateProfile;
use Modules\Affiliate\Models\AffiliateReferral;
use Modules\Affiliate\Models\AffiliateSetting;

class AffiliateCommissionService
{
    public function createForPaidSubscription(array $data): ?AffiliateCommission
    {
        /*
         * Required:
         * workspace_id
         * amount
         *
         * Optional:
         * referred_user_id
         * subscription_id
         * payment_id
         * plan_id
         * type
         * paid_at
         * currency
         */

        $settings = AffiliateSetting::current();

        $workspace = Workspace::query()->find($data['workspace_id'] ?? null);

        if (! $workspace) {
            return null;
        }

        $amount = (float) ($data['amount'] ?? 0);

        if ($amount <= 0) {
            return null;
        }

        if (! empty($data['plan_id'])) {
            $plan = Plan::query()->find($data['plan_id']);

            if ($this->isFreePlan($plan)) {
                return null;
            }
        }

        $referral = $this->findReferralForWorkspace($workspace);

        if (! $referral) {
            return null;
        }

        $profile = $referral->affiliateProfile;

        if (! $profile || $profile->status !== 'active') {
            return null;
        }

        /*
         * منع تكرار العمولة على نفس payment_id
         */
        if (! empty($data['payment_id'])) {
            $exists = AffiliateCommission::query()
                ->where('payment_id', $data['payment_id'])
                ->where('affiliate_profile_id', $profile->id)
                ->exists();

            if ($exists) {
                return null;
            }
        }

        $paidAt = ! empty($data['paid_at'])
            ? Carbon::parse($data['paid_at'])
            : now();

        $commissionAmount = $this->calculateCommissionAmount(
            baseAmount: $amount,
            commissionType: $settings->commission_type,
            commissionValue: (float) $settings->commission_value
        );

        if ($commissionAmount <= 0) {
            return null;
        }

        return DB::transaction(function () use (
            $settings,
            $profile,
            $referral,
            $workspace,
            $data,
            $amount,
            $paidAt,
            $commissionAmount
        ) {
            $commission = AffiliateCommission::query()->create([
                'affiliate_profile_id' => $profile->id,
                'affiliate_referral_id' => $referral->id,

                'referred_user_id' => $data['referred_user_id'] ?? $referral->referred_user_id,
                'workspace_id' => $workspace->id,

                'subscription_id' => $data['subscription_id'] ?? null,
                'payment_id' => $data['payment_id'] ?? null,
                'plan_id' => $data['plan_id'] ?? null,

                'type' => $data['type'] ?? 'subscription_payment',

                'base_amount' => $amount,

                'commission_type' => $settings->commission_type,
                'commission_value' => $settings->commission_value,

                'amount' => $commissionAmount,
                'currency' => $data['currency'] ?? $settings->currency,

                'status' => 'pending',

                'earned_at' => $paidAt,
                'available_at' => $paidAt->copy()->addDays((int) $settings->hold_days),

                'notes' => $data['notes'] ?? null,
            ]);

            $referral->update([
                'status' => 'converted',
                'converted_at' => $referral->converted_at ?: now(),
            ]);

            if ($referral->affiliateLink) {
                $referral->affiliateLink->increment('conversions_count');
            }

            $profile->recalculateBalances();

            return $commission;
        });
    }

    public function releaseAvailableCommissions(): int
    {
        $commissions = AffiliateCommission::query()
            ->where('status', 'pending')
            ->whereNotNull('available_at')
            ->where('available_at', '<=', now())
            ->with('affiliateProfile')
            ->get();

        $count = 0;

        foreach ($commissions as $commission) {
            DB::transaction(function () use ($commission, &$count) {
                $commission->update([
                    'status' => 'available',
                ]);

                $commission->affiliateProfile?->recalculateBalances();

                $count++;
            });
        }

        return $count;
    }

    public function cancelCommissionByPaymentId(int|string $paymentId, ?string $notes = null): bool
    {
        $commissions = AffiliateCommission::query()
            ->where('payment_id', $paymentId)
            ->whereIn('status', ['pending', 'available'])
            ->with('affiliateProfile')
            ->get();

        if ($commissions->isEmpty()) {
            return false;
        }

        foreach ($commissions as $commission) {
            DB::transaction(function () use ($commission, $notes) {
                $commission->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now(),
                    'notes' => trim(($commission->notes ? $commission->notes . "\n" : '') . ($notes ?: 'Commission cancelled')),
                ]);

                $commission->affiliateProfile?->recalculateBalances();
            });
        }

        return true;
    }

    private function findReferralForWorkspace(Workspace $workspace): ?AffiliateReferral
    {
        return AffiliateReferral::query()
            ->where('workspace_id', $workspace->id)
            ->whereIn('status', ['registered', 'converted', 'active_subscriber'])
            ->with(['affiliateProfile', 'affiliateLink'])
            ->latest('id')
            ->first();
    }

    private function calculateCommissionAmount(
        float $baseAmount,
        string $commissionType,
        float $commissionValue
    ): float {
        if ($commissionType === 'fixed') {
            return round($commissionValue, 2);
        }

        return round($baseAmount * ($commissionValue / 100), 2);
    }

    private function isFreePlan(?Plan $plan): bool
    {
        if (! $plan) {
            return false;
        }

        /*
         * عدّل هذه الشروط حسب أعمدة plans عندك.
         */
        if (isset($plan->price) && (float) $plan->price <= 0) {
            return true;
        }

        if (isset($plan->monthly_price) && (float) $plan->monthly_price <= 0) {
            return true;
        }

        if (isset($plan->slug) && in_array($plan->slug, ['free', 'trial'], true)) {
            return true;
        }

        return false;
    }
}