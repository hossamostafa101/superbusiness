<?php

namespace App\Services\App\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantOrder;
use Carbon\Carbon;

class RestaurantOrderService
{
    public function updateStatus(RestaurantOrder $order, string $status): RestaurantOrder
    {
        $payload = [
            'status' => $status,
        ];

        if ($status === 'accepted' && ! $order->accepted_at) {
            $payload['accepted_at'] = now();
        }

        if ($status === 'completed' && ! $order->completed_at) {
            $payload['completed_at'] = now();
        }

        if ($status === 'cancelled' && ! $order->cancelled_at) {
            $payload['cancelled_at'] = now();
        }

        if (! in_array($status, ['accepted', 'completed', 'cancelled'], true)) {
            if ($status !== 'accepted') {
                $payload['accepted_at'] = $order->accepted_at;
            }

            if ($status !== 'completed') {
                $payload['completed_at'] = $order->completed_at;
            }

            if ($status !== 'cancelled') {
                $payload['cancelled_at'] = $order->cancelled_at;
            }
        }

        $order->update($payload);

        $this->syncBusinessRequestStatus($order);

        return $order;
    }

    private function syncBusinessRequestStatus(RestaurantOrder $order): void
    {
        $requestStatus = match ($order->status) {
            'new' => 'new',
            'accepted', 'preparing', 'ready' => 'in_progress',
            'completed' => 'completed',
            'cancelled' => 'cancelled',
            default => 'new',
        };

        $order->requestRecord()->update([
            'status' => $requestStatus,
        ]);
    }
}