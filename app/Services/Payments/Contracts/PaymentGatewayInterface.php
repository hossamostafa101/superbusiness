<?php

namespace App\Services\Payments\Contracts;

use App\Models\Payment;

interface PaymentGatewayInterface
{
    public function createCheckout(Payment $payment): Payment;

    public function handleWebhook(array $payload, array $headers = []): void;
}