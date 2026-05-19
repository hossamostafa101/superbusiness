<?php

namespace App\Services\Billing;

use App\Models\Payment;
use App\Models\Plan;
use App\Models\Workspace;
use App\Services\Admin\PaymentService;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function __construct(
        private readonly PaymentGatewayManager $gatewayManager,
        private readonly PaymentService $paymentService
    ) {}

    public function startCheckout(
        Workspace $workspace,
        Plan $plan,
        string $billingCycle,
        string $provider,
        ?UploadedFile $receiptImage = null,
        ?string $reference = null,
        ?string $notes = null
    ): Payment {
        return DB::transaction(function () use (
            $workspace,
            $plan,
            $billingCycle,
            $provider,
            $receiptImage,
            $reference,
            $notes
        ) {
            if ($provider === 'manual') {
                if (! $receiptImage) {
                    throw new \InvalidArgumentException('صورة إثبات الدفع مطلوبة.');
                }

                return $this->paymentService->createManualPayment(
                    workspace: $workspace,
                    plan: $plan,
                    billingCycle: $billingCycle,
                    receiptImage: $receiptImage,
                    reference: $reference,
                    notes: $notes
                );
            }

            $payment = Payment::create([
                'workspace_id' => $workspace->id,
                'plan_id' => $plan->id,
                'amount' => $this->resolveAmount($plan, $billingCycle),
                'currency' => $plan->currency,
                'billing_cycle' => $billingCycle,
                'method' => 'card',
                'provider' => $provider,
                'status' => 'pending',
                'reference' => $reference,
                'notes' => $notes,
                'metadata' => [
                    'workspace_slug' => $workspace->slug,
                    'plan_slug' => $plan->slug,
                    'created_from' => 'billing_checkout',
                ],
            ]);

            return $this->gatewayManager
                ->driver($provider)
                ->createCheckout($payment);
        });
    }

    private function resolveAmount(Plan $plan, string $billingCycle): float
    {
        if ($billingCycle === 'yearly') {
            return (float) ($plan->yearly_price ?? ($plan->monthly_price * 10));
        }

        return (float) $plan->monthly_price;
    }
}