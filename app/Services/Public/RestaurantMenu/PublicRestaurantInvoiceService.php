<?php

namespace App\Services\Public\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantBranch;
use App\Models\RestaurantMenu\RestaurantInvoice;
use App\Models\RestaurantMenu\RestaurantTable;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublicRestaurantInvoiceService
{
    public function __construct(
        private readonly RestaurantInvoicePinService $pinService,
        private readonly RestaurantMenuSettingReader $settingReader,
        private readonly RestaurantInvoiceSessionService $sessionService
    ) {}

    public function openInvoice(
        Workspace $workspace,
        RestaurantBranch $branch,
        ?RestaurantTable $table,
        array $data
    ): array {
        return DB::transaction(function () use ($workspace, $branch, $table, $data) {
            $durationMinutes = $this->settingReader->invoiceDurationMinutes($workspace);
            $pin = $this->pinService->generate();

            $invoice = RestaurantInvoice::create([
                'workspace_id' => $workspace->id,
                'branch_id' => $branch->id,
                'table_id' => $table?->id,
                'invoice_number' => $this->generateInvoiceNumber($workspace),
                'table_number' => $table?->number ?? ($data['table_number'] ?? null),

                'opened_by_name' => $data['customer_name'] ?? null,
                'opened_by_phone' => $data['customer_phone'] ?? null,

                'pin_hash' => $pin['hash'],
                'pin_hint' => $pin['hint'],

                'mode' => 'open_invoice',
                'status' => 'open',

                'subtotal' => 0,
                'discount_total' => 0,
                'delivery_fee' => 0,
                'tax_total' => 0,
                'total' => 0,
                'currency' => 'EGP',

                'opened_at' => now(),
                'expires_at' => now()->addMinutes($durationMinutes),
                'last_activity_at' => now(),

                'metadata' => [
                    'duration_minutes' => $durationMinutes,
                    'opened_from' => 'public_menu',
                ],
            ]);

            $guest = $this->sessionService->createGuest(
                invoice: $invoice,
                customerName: $data['customer_name'] ?? null,
                customerPhone: $data['customer_phone'] ?? null,
                isOwner: true
            );

            return [
                'invoice' => $invoice,
                'guest' => $guest,
                'pin_display' => $pin['display'],
            ];
        });
    }

    public function joinInvoice(
        RestaurantInvoice $invoice,
        array $data
    ): array {
        return DB::transaction(function () use ($invoice, $data) {
            $guest = $this->sessionService->createGuest(
                invoice: $invoice,
                customerName: $data['customer_name'] ?? null,
                customerPhone: $data['customer_phone'] ?? null,
                isOwner: false
            );

            $invoice->update([
                'last_activity_at' => now(),
            ]);

            return [
                'invoice' => $invoice,
                'guest' => $guest,
            ];
        });
    }

    public function closeInvoice(RestaurantInvoice $invoice): RestaurantInvoice
    {
        $invoice->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);

        return $invoice;
    }

    public function extendInvoice(RestaurantInvoice $invoice, int $minutes): RestaurantInvoice
    {
        $base = $invoice->expires_at && $invoice->expires_at->greaterThan(now())
            ? $invoice->expires_at
            : now();

        $invoice->update([
            'expires_at' => $base->copy()->addMinutes($minutes),
            'last_activity_at' => now(),
        ]);

        return $invoice;
    }

    public function recalculateTotals(RestaurantInvoice $invoice): RestaurantInvoice
    {
        $subtotal = $invoice->items()
            ->where('status', '!=', 'cancelled')
            ->sum('line_total');

        $total = $subtotal
            - (float) $invoice->discount_total
            + (float) $invoice->delivery_fee
            + (float) $invoice->tax_total;

        $invoice->update([
            'subtotal' => $subtotal,
            'total' => max(0, $total),
            'last_activity_at' => now(),
        ]);

        return $invoice;
    }

    private function generateInvoiceNumber(Workspace $workspace): string
    {
        do {
            $number = 'INV-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
        } while (
            RestaurantInvoice::query()
                ->where('workspace_id', $workspace->id)
                ->where('invoice_number', $number)
                ->exists()
        );

        return $number;
    }
}