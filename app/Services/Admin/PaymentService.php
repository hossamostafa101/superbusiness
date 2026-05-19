<?php

namespace App\Services\Admin;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Workspace;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    public function createManualPayment(
        Workspace $workspace,
        Plan $plan,
        string $billingCycle,
        UploadedFile $receiptImage,
        ?string $reference = null,
        ?string $notes = null
    ): Payment {
        return DB::transaction(function () use ($workspace, $plan, $billingCycle, $receiptImage, $reference, $notes) {
            $path = $receiptImage->store('payments/receipts', 'public');

            return Payment::create([
                'workspace_id' => $workspace->id,
                'plan_id' => $plan->id,
                'amount' => $billingCycle === 'yearly'
                    ? ($plan->yearly_price ?? $plan->monthly_price * 10)
                    : $plan->monthly_price,
                'currency' => $plan->currency,
                'billing_cycle' => $billingCycle,
                'method' => 'manual',
                'provider' => 'manual',
                'status' => 'pending',
                'reference' => $reference,
                'receipt_image' => $path,
                'notes' => $notes,
            ]);
        });
    }

    public function approveManualPayment(Payment $payment, int $adminId): Payment
    {
        return DB::transaction(function () use ($payment, $adminId) {
            if (! $payment->isManual()) {
                throw new \RuntimeException('هذا الإجراء مخصص للمدفوعات اليدوية فقط.');
            }

            if (! $payment->isPending()) {
                throw new \RuntimeException('لا يمكن اعتماد دفعة ليست قيد المراجعة.');
            }

            $payment->update([
                'status' => 'approved',
                'approved_by' => $adminId,
                'approved_at' => now(),
                'paid_at' => now(),
            ]);

            app(SubscriptionService::class)->activateFromPayment($payment);

            return $payment->fresh();
        });
    }

    public function rejectManualPayment(Payment $payment, int $adminId, ?string $reason = null): Payment
    {
        if (! $payment->isManual()) {
            throw new \RuntimeException('هذا الإجراء مخصص للمدفوعات اليدوية فقط.');
        }

        if (! $payment->isPending()) {
            throw new \RuntimeException('لا يمكن رفض دفعة ليست قيد المراجعة.');
        }

        $payment->update([
            'status' => 'rejected',
            'approved_by' => $adminId,
            'rejected_at' => now(),
            'notes' => $reason ?: $payment->notes,
        ]);

        return $payment;
    }
}