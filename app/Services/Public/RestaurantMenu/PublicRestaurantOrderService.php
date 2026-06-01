<?php

namespace App\Services\Public\RestaurantMenu;

use App\Models\BusinessRequest;
use App\Models\RestaurantMenu\RestaurantBranch;
use App\Models\RestaurantMenu\RestaurantInvoice;
use App\Models\RestaurantMenu\RestaurantOrder;
use App\Models\RestaurantMenu\RestaurantTable;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublicRestaurantOrderService
{
    public function __construct(
        private readonly RestaurantOrderPricingService $pricingService,
        private readonly RestaurantInvoiceSessionService $invoiceSessionService,
        private readonly PublicRestaurantInvoiceService $invoiceService
    ) {}

    public function createOrder(
        Workspace $workspace,
        RestaurantBranch $branch,
        array $data
    ): RestaurantOrder {
        return DB::transaction(function () use ($workspace, $branch, $data) {
            $pricing = $this->pricingService->buildPricingPayload(
                workspace: $workspace,
                branch: $branch,
                items: $data['items']
            );


            $table = null;

            if (! empty($data['table_code'])) {
                $table = RestaurantTable::query()
                    ->where('workspace_id', $workspace->id)
                    ->where('branch_id', $branch->id)
                    ->where('code', $data['table_code'])
                    ->where('is_active', true)
                    ->first();
            }

            $invoice = null;
            $invoiceGuest = null;

            if (! empty($data['invoice_id'])) {
                $invoice = RestaurantInvoice::query()
                    ->where('workspace_id', $workspace->id)
                    ->where('branch_id', $branch->id)
                    ->where('status', 'open')
                    ->where(function ($query) {
                        $query->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    })
                    ->find($data['invoice_id']);

                if ($invoice) {
                    $invoiceGuest = $this->invoiceSessionService->currentGuest($invoice);

                    if (! $invoiceGuest) {
                        throw new \RuntimeException('غير مصرح بالإضافة إلى هذه الفاتورة.');
                    }
                }
            }


            $customerAccountId = session('public_customer_account_id_' . $workspace->id);






            $deliverySettings = app(\App\Services\App\RestaurantMenu\RestaurantDeliverySettingsService::class)
                ->get($workspace, $branch);

            $deliveryZone = null;
            $deliveryFee = 0;

            if (($data['order_type'] ?? null) === 'delivery') {
                if (! empty($data['delivery_zone_id'])) {
                    $deliveryZone = \App\Models\RestaurantMenu\RestaurantDeliveryZone::query()
                        ->where('workspace_id', $workspace->id)
                        ->where('is_active', true)
                        ->where(function ($query) use ($branch) {
                            $query->whereNull('branch_id')
                                ->orWhere('branch_id', $branch->id);
                        })
                        ->whereKey($data['delivery_zone_id'])
                        ->first();
                }

                if ($deliverySettings->fee_calculation_mode === 'zone' && $deliveryZone) {
                    $deliveryFee = (float) $deliveryZone->delivery_fee;
                }

                if ($deliverySettings->fee_calculation_mode === 'free') {
                    $deliveryFee = 0;
                }
            }

            $subtotal = (float) $pricing['subtotal'];
$discountTotal = (float) ($pricing['discount_total'] ?? 0);
$taxTotal = (float) ($pricing['tax_total'] ?? 0);

if (
    ($data['order_type'] ?? null) === 'delivery'
    && $deliveryZone
    && ! is_null($deliveryZone->min_order_amount)
    && $subtotal < (float) $deliveryZone->min_order_amount
) {
    throw \Illuminate\Validation\ValidationException::withMessages([
        'delivery_zone_id' => 'الحد الأدنى للتوصيل لهذه المنطقة هو '
            . number_format((float) $deliveryZone->min_order_amount, 2),
    ]);
}

$total = $subtotal - $discountTotal + $taxTotal;

if (
    ($data['order_type'] ?? null) === 'delivery'
    && (bool) $deliverySettings->delivery_fee_included_in_total
) {
    $total += $deliveryFee;
}




            $order = RestaurantOrder::create([
                'workspace_id' => $workspace->id,
                'branch_id' => $branch->id,
                'order_number' => $this->generateOrderNumber($workspace),

                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,

                'order_type' => $data['order_type'] ?? 'takeaway',
                'table_id' => $table?->id,
                'table_number' => $table?->number ?? ($data['table_number'] ?? null),
                'delivery_address' => $data['delivery_address'] ?? null,
                'delivery_zone_id' => $deliveryZone?->id,

'delivery_fee_included_in_total' => (bool) $deliverySettings->delivery_fee_included_in_total,
'show_delivery_fee_on_receipt' => (bool) $deliverySettings->show_delivery_fee_on_receipt,
'delivery_fee_payment_target' => $deliverySettings->delivery_fee_payment_target,
'delivery_status' => $deliverySettings->default_delivery_status,

'delivery_address_details' => $data['delivery_address_details'] ?? null,
'delivery_area' => $data['delivery_area'] ?? null,
'delivery_building' => $data['delivery_building'] ?? null,
'delivery_floor' => $data['delivery_floor'] ?? null,
'delivery_apartment' => $data['delivery_apartment'] ?? null,
'delivery_landmark' => $data['delivery_landmark'] ?? null,

                'notes' => $data['notes'] ?? null,

                // 'subtotal' => $pricing['subtotal'],
                // 'discount_total' => $pricing['discount_total'],
                // 'delivery_fee' => $pricing['delivery_fee'],
                // 'tax_total' => $pricing['tax_total'],
                // 'total' => $pricing['total'],
                // 'currency' => $pricing['currency'],
                'subtotal' => $subtotal,
'discount_total' => $discountTotal,
'delivery_fee' => $deliveryFee,
'tax_total' => $taxTotal,
'total' => $total,
'currency' => $pricing['currency'],

                'status' => 'new',
                'payment_status' => 'unpaid',
                'payment_method' => null,
                'source' => 'public_menu',

                'metadata' => [
                    'created_from' => 'public_restaurant_menu',
                    'branch_slug' => $branch->slug,
                ],


                'invoice_id' => $invoice?->id,

                'customer_account_id' => $customerAccountId,
            ]);

            foreach ($pricing['lines'] as $line) {

                $isOfferLine = ($line['line_type'] ?? 'item') === 'offer';

                $orderItem = $order->items()->create([
                    'workspace_id' => $workspace->id,
                    'branch_id' => $branch->id,

                    'line_type' => $isOfferLine ? 'offer' : 'item',
                    'offer_id' => $isOfferLine ? $line['offer']->id : null,

                    'item_id' => $isOfferLine ? null : $line['item']->id,
                    'variant_id' => $isOfferLine ? null : $line['variant']?->id,

                    'item_name' => $isOfferLine ? $line['offer']->title : $line['item']->name,
                    'variant_name' => $isOfferLine ? null : $line['variant']?->name,

                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'options_total' => $line['options_total'],
                    'line_total' => $line['line_total'],
                    'currency' => $line['currency'],
                    'notes' => $line['notes'],

                    'metadata' => $isOfferLine
                        ? [
                            'offer_snapshot' => [
                                'title' => $line['offer']->title,
                                'subtitle' => $line['offer']->subtitle,
                                'description' => $line['offer']->description,
                                'image' => $line['offer']->image,
                                'old_price' => $line['offer']->old_price,
                                // 'new_price' => $line['offer']->new_price,
                                'new_price' => $line['offer']->new_price ?? $line['offer']->price ?? null,
                            ],
                        ]
                        : [
                            'item_snapshot' => [
                                'description' => $line['item']->description,
                                'image' => $line['item']->image,
                            ],
                        ],
                ]);





                if ($invoice) {
                    $invoiceItem = $invoice->items()->create([
                        'workspace_id' => $workspace->id,
                        'branch_id' => $branch->id,
                        'guest_id' => $invoiceGuest?->id,
                        'order_id' => $order->id,

                        'line_type' => $isOfferLine ? 'offer' : 'item',
                        'offer_id' => $isOfferLine ? $line['offer']->id : null,

                        'item_id' => $isOfferLine ? null : $line['item']->id,
                        'variant_id' => $isOfferLine ? null : $line['variant']?->id,

                        'item_name' => $isOfferLine ? $line['offer']->title : $line['item']->name,
                        'variant_name' => $isOfferLine ? null : $line['variant']?->name,

                        'quantity' => $line['quantity'],
                        'unit_price' => $line['unit_price'],
                        'options_total' => $line['options_total'],
                        'line_total' => $line['line_total'],
                        'currency' => $line['currency'],
                        'notes' => $line['notes'],
                        'status' => 'new',
                        'metadata' => [
                            'order_item_id' => $orderItem->id,
                        ],
                    ]);
                }





                foreach ($line['options'] as $option) {
                    $orderItem->options()->create([
                        'workspace_id' => $workspace->id,
                        'branch_id' => $branch->id,
                        'order_id' => $order->id,

                        'option_group_id' => $option->option_group_id,
                        'option_id' => $option->id,

                        'group_name' => $option->group?->name,
                        'option_name' => $option->name,

                        'price' => $option->price,
                        'currency' => $option->currency,
                    ]);

                    if ($invoice && isset($invoiceItem)) {
                        $invoiceItem->options()->create([
                            'workspace_id' => $workspace->id,
                            'branch_id' => $branch->id,
                            'invoice_id' => $invoice->id,

                            'option_group_id' => $option->option_group_id,
                            'option_id' => $option->id,

                            'group_name' => $option->group?->name,
                            'option_name' => $option->name,

                            'price' => $option->price,
                            'currency' => $option->currency,
                        ]);
                    }
                }
            }

            if ($invoice) {
                $this->invoiceService->recalculateTotals($invoice);
            }
            $this->createBusinessRequest($workspace, $order);

            return $order->load(['items.options', 'branch']);
        });
    }

    private function createBusinessRequest(Workspace $workspace, RestaurantOrder $order): void
    {
        BusinessRequest::create([
            'workspace_id' => $workspace->id,

            'type' => 'restaurant_order',
            'source' => 'restaurant_menu',

            'reference_type' => RestaurantOrder::class,
            'reference_id' => $order->id,

            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'customer_email' => $order->customer_email,

            'title' => 'طلب منيو جديد #' . $order->order_number,
            'message' => 'طلب جديد بقيمة ' . number_format((float) $order->total, 2) . ' ' . $order->currency,

            'status' => 'new',
            'priority' => 'normal',

            'metadata' => [
                'order_number' => $order->order_number,
                'order_type' => $order->order_type,
                'branch_id' => $order->branch_id,
                'total' => $order->total,
                'currency' => $order->currency,
            ],
        ]);
    }

    private function generateOrderNumber(Workspace $workspace): string
    {
        do {
            // $number = 'ORD-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
            $number = now()->format('ymd') . random_int(1000, 9999);
        } while (
            RestaurantOrder::query()
            ->where('workspace_id', $workspace->id)
            ->where('order_number', $number)
            ->exists()
        );

        return $number;
    }
}
