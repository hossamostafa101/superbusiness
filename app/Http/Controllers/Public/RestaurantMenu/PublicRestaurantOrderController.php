<?php

namespace App\Http\Controllers\Public\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\RestaurantMenu\StorePublicRestaurantOrderRequest;
use App\Models\RestaurantMenu\RestaurantBranch;
use App\Models\RestaurantMenu\RestaurantOrder;
use App\Models\Workspace;
use App\Services\Core\FeatureLimitService;
use App\Services\Public\RestaurantMenu\PublicRestaurantOrderService;

class PublicRestaurantOrderController extends Controller
{
    public function __construct(
        private readonly PublicRestaurantOrderService $publicRestaurantOrderService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function store(StorePublicRestaurantOrderRequest $request, Workspace $workspace, RestaurantBranch $branch)
    {
        abort_if($workspace->status !== 'active', 404);
        abort_if((int) $branch->workspace_id !== (int) $workspace->id, 404);
        abort_if(! $branch->is_active, 404);

        if (! $this->featureLimitService->enabled($workspace, 'restaurant_orders_enabled')) {
            return back()
                ->withInput()
                ->with('error', 'استقبال الطلبات غير متاح حاليًا.');
        }

        $currentCount = $workspace->restaurantOrders()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_orders_limit', $currentCount)) {
            return back()
                ->withInput()
                ->with('error', 'لا يمكن استقبال طلبات جديدة حاليًا.');
        }

        $orderType = $request->input('order_type');

        if ($orderType === 'dine_in' && ! $this->featureLimitService->enabled($workspace, 'restaurant_dine_in_enabled')) {
            return back()->withInput()->with('error', 'طلبات داخل المكان غير متاحة حاليًا.');
        }

        if ($orderType === 'takeaway' && ! $this->featureLimitService->enabled($workspace, 'restaurant_takeaway_enabled')) {
            return back()->withInput()->with('error', 'طلبات التيك أواي غير متاحة حاليًا.');
        }

        if ($orderType === 'delivery' && ! $this->featureLimitService->enabled($workspace, 'restaurant_delivery_enabled')) {
            return back()->withInput()->with('error', 'طلبات الدليفري غير متاحة حاليًا.');
        }

        try {
            $order = $this->publicRestaurantOrderService->createOrder(
                workspace: $workspace,
                branch: $branch,
                data: $request->validated()
            );

            if ($order->invoice_id) {
                return redirect()
                    ->route('public.restaurant-menu.invoices.show', [$workspace, $branch, $order->invoice_id])
                    ->with('success', 'تمت إضافة الطلب إلى الفاتورة.');
            }

            return redirect()
    ->route('public.restaurant-menu.orders.track', [$workspace, $branch, $order])
    ->with('success', 'تم إرسال الطلب بنجاح.');
    
            // return redirect()
            //     ->route('public.restaurant-menu.order-success', [$workspace, $branch, $order])
            //     ->with('success', 'تم إرسال الطلب بنجاح.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function success(Workspace $workspace, RestaurantBranch $branch, RestaurantOrder $restaurantOrder)
    {
        abort_if($workspace->status !== 'active', 404);
        abort_if((int) $branch->workspace_id !== (int) $workspace->id, 404);
        abort_if((int) $restaurantOrder->workspace_id !== (int) $workspace->id, 404);
        abort_if((int) $restaurantOrder->branch_id !== (int) $branch->id, 404);

        $restaurantOrder->load(['items.options', 'branch']);

        return view('public.restaurant-menu.order-success', compact(
            'workspace',
            'branch',
            'restaurantOrder'
        ));
    }











    public function track(Workspace $workspace, RestaurantBranch $branch, RestaurantOrder $restaurantOrder)
{
    abort_if($workspace->status !== 'active', 404);
    abort_if((int) $branch->workspace_id !== (int) $workspace->id, 404);
    abort_if((int) $restaurantOrder->workspace_id !== (int) $workspace->id, 404);
    abort_if((int) $restaurantOrder->branch_id !== (int) $branch->id, 404);

    $restaurantOrder->load(['items.options', 'branch']);

    return view('public.restaurant-menu.order-track', compact(
        'workspace',
        'branch',
        'restaurantOrder'
    ));
}

public function status(Workspace $workspace, RestaurantBranch $branch, RestaurantOrder $restaurantOrder)
{
    abort_if($workspace->status !== 'active', 404);
    abort_if((int) $branch->workspace_id !== (int) $workspace->id, 404);
    abort_if((int) $restaurantOrder->workspace_id !== (int) $workspace->id, 404);
    abort_if((int) $restaurantOrder->branch_id !== (int) $branch->id, 404);

    return response()->json([
        'id' => $restaurantOrder->id,
        'order_number' => $restaurantOrder->order_number,
        'status' => $restaurantOrder->status,
        'status_label' => $restaurantOrder->statusLabel(),
        'progress_percent' => $this->orderProgressPercent($restaurantOrder->status),
        'is_done' => in_array($restaurantOrder->status, ['completed', 'cancelled'], true),
        'updated_at' => $restaurantOrder->updated_at?->format('H:i:s'),
    ]);
}

private function orderProgressPercent(string $status): int
{
    return match ($status) {
        'new' => 20,
        'accepted' => 40,
        'preparing' => 65,
        'ready' => 85,
        'completed' => 100,
        'cancelled' => 100,
        default => 20,
    };
}
}
