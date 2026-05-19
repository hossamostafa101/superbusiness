<?php

namespace App\Services\Payments\Gateways;

use App\Models\Payment;
use App\Services\Payments\Contracts\PaymentGatewayInterface;

class ManualPaymentGateway implements PaymentGatewayInterface
{
    public function createCheckout(Payment $payment): Payment
    {
        return $payment;
    }

    public function handleWebhook(array $payload, array $headers = []): void
    {
        // Manual payment has no webhook.
    }
}