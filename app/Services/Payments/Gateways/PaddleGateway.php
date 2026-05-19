<?php

namespace App\Services\Payments\Gateways;

use App\Models\Payment;
use App\Services\Payments\Contracts\PaymentGatewayInterface;

class PaddleGateway implements PaymentGatewayInterface
{
    public function createCheckout(Payment $payment): Payment
    {
        $payment->loadMissing('workspace');

        $payment->update([
            'provider' => 'paddle',
            'method' => 'card',
            'status' => 'pending',
            'provider_reference' => 'PADDLE-LOCAL-' . $payment->id,
            'checkout_url' => route('billing.success', $payment->workspace),
            'metadata' => array_merge($payment->metadata ?? [], [
                'gateway' => 'paddle',
                'mode' => 'placeholder',
            ]),
        ]);

        return $payment->fresh();
    }

    public function handleWebhook(array $payload, array $headers = []): void
    {
        //
    }
}