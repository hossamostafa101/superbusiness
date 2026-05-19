<?php

namespace App\Services\Payments;

use App\Services\Payments\Contracts\PaymentGatewayInterface;
use App\Services\Payments\Gateways\KashierGateway;
use App\Services\Payments\Gateways\ManualPaymentGateway;
use App\Services\Payments\Gateways\PaddleGateway;
use InvalidArgumentException;

class PaymentGatewayManager
{
    public function driver(string $provider): PaymentGatewayInterface
    {
        return match ($provider) {
            'manual' => app(ManualPaymentGateway::class),
            'kashier' => app(KashierGateway::class),
            'paddle' => app(PaddleGateway::class),
            default => throw new InvalidArgumentException("Unsupported payment provider: {$provider}"),
        };
    }
}