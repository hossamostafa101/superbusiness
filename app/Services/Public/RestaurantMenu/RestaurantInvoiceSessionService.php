<?php

namespace App\Services\Public\RestaurantMenu;

use App\Models\RestaurantMenu\RestaurantBranch;
use App\Models\RestaurantMenu\RestaurantInvoice;
use App\Models\RestaurantMenu\RestaurantInvoiceGuest;
use App\Models\RestaurantMenu\RestaurantTable;
use App\Models\Workspace;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class RestaurantInvoiceSessionService
{
    public function cookieName(RestaurantInvoice $invoice): string
    {
        return 'restaurant_invoice_guest_' . $invoice->id;
    }

    public function findOpenInvoiceForTable(
        Workspace $workspace,
        RestaurantBranch $branch,
        ?RestaurantTable $table
    ): ?RestaurantInvoice {
        if (! $table) {
            return null;
        }

        $invoice = RestaurantInvoice::query()
            ->where('workspace_id', $workspace->id)
            ->where('branch_id', $branch->id)
            ->where('table_id', $table->id)
            ->where('status', 'open')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->latest('id')
            ->first();

        if ($invoice && $invoice->isExpired()) {
            $invoice->update([
                'status' => 'expired',
                'closed_at' => now(),
            ]);

            return null;
        }

        return $invoice;
    }

    public function currentGuest(RestaurantInvoice $invoice): ?RestaurantInvoiceGuest
    {
        $token = request()->cookie($this->cookieName($invoice));

        if (! $token) {
            return null;
        }

        return $invoice->guests()
            ->where('guest_token', $token)
            ->first();
    }

    public function createGuest(
        RestaurantInvoice $invoice,
        ?string $customerName = null,
        ?string $customerPhone = null,
        bool $isOwner = false
    ): RestaurantInvoiceGuest {
        $guest = $invoice->guests()->create([
            'workspace_id' => $invoice->workspace_id,
            'branch_id' => $invoice->branch_id,
            'guest_token' => Str::random(64),
            'customer_name' => $customerName,
            'customer_phone' => $customerPhone,
            'device_label' => request()->userAgent()
                ? Str::limit(request()->userAgent(), 110)
                : null,
            'is_owner' => $isOwner,
            'joined_at' => now(),
            'last_seen_at' => now(),
        ]);

        Cookie::queue(
            $this->cookieName($invoice),
            $guest->guest_token,
            max(30, now()->diffInMinutes($invoice->expires_at ?? now()->addHours(2)))
        );

        return $guest;
    }

    public function touchGuest(RestaurantInvoiceGuest $guest): void
    {
        $guest->update([
            'last_seen_at' => now(),
        ]);
    }
}