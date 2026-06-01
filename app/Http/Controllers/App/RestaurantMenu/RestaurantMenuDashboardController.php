<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Services\App\RestaurantMenu\RestaurantMenuSettingsService;

class RestaurantMenuDashboardController extends Controller
{
    public function __invoke(Workspace $workspace)
    {
        $now = now();

        $settings = app(RestaurantMenuSettingsService::class)
            ->values($workspace);

        $orderingMode = $settings['restaurant_ordering_mode'] ?? 'single_order';

        $openInvoiceEnabled = $orderingMode === 'open_invoice';

        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */
        $activeStatuses = ['new', 'accepted', 'preparing', 'ready'];

        $newOrdersCount = $workspace->restaurantOrders()
            ->where('status', 'new')
            ->count();

        $acceptedOrdersCount = $workspace->restaurantOrders()
            ->where('status', 'accepted')
            ->count();

        $preparingOrdersCount = $workspace->restaurantOrders()
            ->where('status', 'preparing')
            ->count();

        $readyOrdersCount = $workspace->restaurantOrders()
            ->where('status', 'ready')
            ->count();

        $activeOrdersCount = $workspace->restaurantOrders()
            ->whereIn('status', $activeStatuses)
            ->count();

        $todayOrdersCount = $workspace->restaurantOrders()
            ->whereDate('created_at', today())
            ->count();

        $completedTodayOrdersCount = $workspace->restaurantOrders()
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->count();

        $cancelledTodayOrdersCount = $workspace->restaurantOrders()
            ->whereDate('created_at', today())
            ->where('status', 'cancelled')
            ->count();

        $todaySalesTotal = $workspace->restaurantOrders()
            ->whereDate('created_at', today())
            ->whereNotIn('status', ['cancelled'])
            ->sum('total');

        $yesterdaySalesTotal = $workspace->restaurantOrders()
            ->whereDate('created_at', today()->subDay())
            ->whereNotIn('status', ['cancelled'])
            ->sum('total');

        $salesChangePercent = null;

        if ((float) $yesterdaySalesTotal > 0) {
            $salesChangePercent = round(
                (((float) $todaySalesTotal - (float) $yesterdaySalesTotal) / (float) $yesterdaySalesTotal) * 100,
                1
            );
        }

        $latestOrders = $workspace->restaurantOrders()
            ->with('branch:id,name')
            ->withCount('items')
            ->latest('id')
            ->limit(8)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Open Invoices / Table Sessions
        |--------------------------------------------------------------------------
        */
        $openInvoicesCount = 0;
        $endingSoonInvoicesCount = 0;
        $expiredOpenInvoicesCount = 0;
        $latestOpenInvoices = collect();

        if ($openInvoiceEnabled) {
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

            $latestOpenInvoices = $workspace->restaurantInvoices()
                ->with([
                    'branch:id,name',
                    'table:id,name,number',
                ])
                ->where('status', 'open')
                ->latest('id')
                ->limit(8)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Service Requests
        |--------------------------------------------------------------------------
        */
        $pendingServiceRequestsCount = 0;

        if (method_exists($workspace, 'restaurantTableServiceRequests')) {
            $pendingServiceRequestsCount = $workspace->restaurantTableServiceRequests()
                ->whereIn('status', ['new', 'seen'])
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | POS Shifts
        |--------------------------------------------------------------------------
        */
        $openShiftsCount = 0;
        $latestOpenShifts = collect();

        if (method_exists($workspace, 'restaurantPosShifts')) {
            $openShiftsCount = $workspace->restaurantPosShifts()
                ->where('status', 'open')
                ->count();

            $latestOpenShifts = $workspace->restaurantPosShifts()
                ->with([
                    'branch:id,name',
                    'register:id,name',
                    'staff:id,name',
                ])
                ->where('status', 'open')
                ->latest('id')
                ->limit(5)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Quick Data
        |--------------------------------------------------------------------------
        */
        $branchesCount = $workspace->restaurantBranches()->count();

        $itemsCount = method_exists($workspace, 'restaurantMenuItems')
            ? $workspace->restaurantMenuItems()->count()
            : 0;

        $availableItemsCount = method_exists($workspace, 'restaurantMenuItems')
            ? $workspace->restaurantMenuItems()->where('is_available', true)->count()
            : 0;

        $defaultBranch = $workspace->restaurantBranches()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        return view('app.restaurant-menu.dashboard.index', compact(
            'workspace',
            'orderingMode',
            'openInvoiceEnabled',

            'newOrdersCount',
            'acceptedOrdersCount',
            'preparingOrdersCount',
            'readyOrdersCount',
            'activeOrdersCount',
            'todayOrdersCount',
            'completedTodayOrdersCount',
            'cancelledTodayOrdersCount',
            'todaySalesTotal',
            'yesterdaySalesTotal',
            'salesChangePercent',
            'latestOrders',

            'openInvoicesCount',
            'endingSoonInvoicesCount',
            'expiredOpenInvoicesCount',
            'latestOpenInvoices',

            'pendingServiceRequestsCount',

            'openShiftsCount',
            'latestOpenShifts',

            'branchesCount',
            'itemsCount',
            'availableItemsCount',
            'defaultBranch'
        ));
    }
}