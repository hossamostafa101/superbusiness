<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Models\Workspace;

class RestaurantMenuDashboardController extends Controller
{
    public function __invoke(Workspace $workspace)
    {
        $now = now();

        $newOrdersCount = $workspace->restaurantOrders()
            ->where('status', 'new')
            ->count();

        $openInvoicesCount = $workspace->restaurantInvoices()
            ->where('status', 'open')
            ->where(function ($query) use ($now) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', $now);
            })
            ->count();

        $endingSoonInvoicesCount = $workspace->restaurantInvoices()
            ->where('status', 'open')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', $now)
            ->where('expires_at', '<=', $now->copy()->addMinutes(10))
            ->count();

        $expiredOpenInvoicesCount = $workspace->restaurantInvoices()
            ->where('status', 'open')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->count();

        $todayOrdersCount = $workspace->restaurantOrders()
            ->whereDate('created_at', today())
            ->count();

        $todaySalesTotal = $workspace->restaurantOrders()
            ->whereDate('created_at', today())
            ->whereNotIn('status', ['cancelled'])
            ->sum('total');

        $latestOrders = $workspace->restaurantOrders()
            ->with('branch:id,name')
            ->latest('id')
            ->limit(8)
            ->get();

        $latestOpenInvoices = $workspace->restaurantInvoices()
            ->with([
                'branch:id,name',
                'table:id,name,number',
            ])
            ->where('status', 'open')
            ->latest('id')
            ->limit(8)
            ->get();

            $settings = app(\App\Services\App\RestaurantMenu\RestaurantMenuSettingsService::class)
    ->values($workspace);

$orderingMode = $settings['restaurant_ordering_mode'] ?? 'single_order';

        return view('app.restaurant-menu.dashboard.index', compact(
            'workspace',
            'newOrdersCount',
            'openInvoicesCount',
            'endingSoonInvoicesCount',
            'expiredOpenInvoicesCount',
            'todayOrdersCount',
            'todaySalesTotal',
            'latestOrders',
            'latestOpenInvoices',
            'orderingMode'
        ));
    }
}