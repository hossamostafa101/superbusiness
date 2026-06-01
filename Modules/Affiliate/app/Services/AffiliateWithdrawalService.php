<?php

namespace Modules\Affiliate\Services;

use Illuminate\Support\Facades\DB;
use Modules\Affiliate\Models\AffiliateProfile;
use Modules\Affiliate\Models\AffiliateSetting;
use Modules\Affiliate\Models\AffiliateWithdrawal;

class AffiliateWithdrawalService
{
    public function requestWithdrawal(AffiliateProfile $profile, array $data = []): AffiliateWithdrawal
    {
        $settings = AffiliateSetting::current();

        $profile->recalculateBalances();
        $profile->refresh();

        if ($profile->status !== 'active') {
            throw new \RuntimeException('حساب المسوق غير نشط.');
        }

        if ((float) $profile->available_balance < (float) $settings->minimum_withdrawal_amount) {
            throw new \RuntimeException('الرصيد المتاح أقل من الحد الأدنى للسحب.');
        }

        $availableCommissions = $profile->commissions()
            ->where('status', 'available')
            ->orderBy('available_at')
            ->orderBy('id')
            ->get();

        if ($availableCommissions->isEmpty()) {
            throw new \RuntimeException('لا توجد عمولات متاحة للسحب.');
        }

        $amount = (float) $availableCommissions->sum('amount');

        return DB::transaction(function () use ($profile, $settings, $data, $availableCommissions, $amount) {
            $withdrawal = $profile->withdrawals()->create([
                'withdrawal_number' => $this->generateWithdrawalNumber(),

                'amount' => $amount,
                'currency' => $settings->currency,

                'status' => 'requested',

                'payment_method' => $data['payment_method'] ?? $profile->payment_method,
                'payment_details' => $data['payment_details'] ?? $profile->payment_details,

                'requested_at' => now(),
                'affiliate_notes' => $data['affiliate_notes'] ?? null,
            ]);

            foreach ($availableCommissions as $commission) {
                $withdrawal->commissions()->attach($commission->id, [
                    'amount' => $commission->amount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return $withdrawal;
        });
    }

    public function approve(AffiliateWithdrawal $withdrawal, ?string $adminNotes = null): AffiliateWithdrawal
    {
        if ($withdrawal->status !== 'requested') {
            throw new \RuntimeException('لا يمكن قبول هذا الطلب في حالته الحالية.');
        }

        $withdrawal->update([
            'status' => 'approved',
            'approved_at' => now(),
            'admin_notes' => $adminNotes,
        ]);

        return $withdrawal;
    }

    public function markAsPaid(AffiliateWithdrawal $withdrawal, ?string $adminNotes = null): AffiliateWithdrawal
    {
        if (! in_array($withdrawal->status, ['requested', 'approved'], true)) {
            throw new \RuntimeException('لا يمكن دفع هذا الطلب في حالته الحالية.');
        }

        return DB::transaction(function () use ($withdrawal, $adminNotes) {
            $withdrawal->loadMissing('commissions', 'affiliateProfile');

            foreach ($withdrawal->commissions as $commission) {
                if ($commission->status === 'available') {
                    $commission->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);
                }
            }

            $withdrawal->update([
                'status' => 'paid',
                'paid_at' => now(),
                'admin_notes' => $adminNotes ?? $withdrawal->admin_notes,
            ]);

            $withdrawal->affiliateProfile?->recalculateBalances();

            return $withdrawal;
        });
    }

    public function reject(AffiliateWithdrawal $withdrawal, ?string $adminNotes = null): AffiliateWithdrawal
    {
        if (! in_array($withdrawal->status, ['requested', 'approved'], true)) {
            throw new \RuntimeException('لا يمكن رفض هذا الطلب في حالته الحالية.');
        }

        $withdrawal->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'admin_notes' => $adminNotes,
        ]);

        return $withdrawal;
    }

    private function generateWithdrawalNumber(): string
    {
        do {
            $number = 'WD' . now()->format('ymd') . random_int(1000, 9999);
        } while (
            AffiliateWithdrawal::query()
                ->where('withdrawal_number', $number)
                ->exists()
        );

        return $number;
    }
}