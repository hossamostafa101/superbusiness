<?php

namespace App\Services\App\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantInvoice;
use App\Services\Public\RestaurantMenu\PublicRestaurantInvoiceService;

class RestaurantInvoiceService
{
    public function __construct(
        private readonly PublicRestaurantInvoiceService $publicRestaurantInvoiceService
    ) {}

    public function close(RestaurantInvoice $invoice): RestaurantInvoice
    {
        $invoice->update([
            'status' => 'closed',
            'closed_at' => now(),
            'last_activity_at' => now(),
        ]);

        return $invoice;
    }

    public function cancel(RestaurantInvoice $invoice): RestaurantInvoice
    {
        $invoice->update([
            'status' => 'cancelled',
            'closed_at' => now(),
            'last_activity_at' => now(),
        ]);

        return $invoice;
    }

    public function expire(RestaurantInvoice $invoice): RestaurantInvoice
    {
        $invoice->update([
            'status' => 'expired',
            'closed_at' => now(),
            'last_activity_at' => now(),
        ]);

        return $invoice;
    }

    public function extend(RestaurantInvoice $invoice, int $minutes): RestaurantInvoice
    {
        return $this->publicRestaurantInvoiceService->extendInvoice(
            invoice: $invoice,
            minutes: $minutes
        );
    }

    public function recalculate(RestaurantInvoice $invoice): RestaurantInvoice
    {
        return $this->publicRestaurantInvoiceService->recalculateTotals($invoice);
    }
}