<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Models\RestaurantMenu\RestaurantOrder;
use App\Models\Workspace;
use App\Services\App\RestaurantMenu\RestaurantOrderService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RestaurantOrderController extends Controller
{
    public function __construct(
        private readonly RestaurantOrderService $restaurantOrderService
    ) {}

    // public function index(Request $request, Workspace $workspace)
    // {
    //     $orders = $workspace->restaurantOrders()
    //         ->with([
    //             'branch:id,name',
    //         ])
    //         ->withCount('items')
    //         ->when($request->filled('search'), function ($query) use ($request) {
    //             $search = trim($request->input('search'));

    //             $query->where(function ($q) use ($search) {
    //                 $q->where('order_number', 'like', "%{$search}%")
    //                     ->orWhere('customer_name', 'like', "%{$search}%")
    //                     ->orWhere('customer_phone', 'like', "%{$search}%")
    //                     ->orWhere('customer_email', 'like', "%{$search}%");
    //             });
    //         })
    //         ->when($request->filled('branch_id'), function ($query) use ($request) {
    //             $query->where('branch_id', $request->input('branch_id'));
    //         })
    //         ->when($request->filled('status'), function ($query) use ($request) {
    //             $query->where('status', $request->input('status'));
    //         })
    //         ->when($request->filled('order_type'), function ($query) use ($request) {
    //             $query->where('order_type', $request->input('order_type'));
    //         })
    //         ->when($request->filled('date_from'), function ($query) use ($request) {
    //             $query->whereDate('created_at', '>=', $request->input('date_from'));
    //         })
    //         ->when($request->filled('date_to'), function ($query) use ($request) {
    //             $query->whereDate('created_at', '<=', $request->input('date_to'));
    //         })
    //         ->latest('id')
    //         ->paginate(15)
    //         ->withQueryString();

    //     $branches = $workspace->restaurantBranches()
    //         ->orderBy('sort_order')
    //         ->orderBy('name')
    //         ->get(['id', 'name']);

    //     return view('app.restaurant-menu.orders.index', compact(
    //         'workspace',
    //         'orders',
    //         'branches'
    //     ));
    // }

    public function index(Request $request, Workspace $workspace)
{
    $status = $request->input('status', 'active');

    $ordersQuery = $workspace->restaurantOrders()
        ->with([
            'branch:id,name',
            'items',
        ])
        ->latest('id');

    if ($status === 'active') {
        $ordersQuery->whereIn('status', [
            'new',
            'accepted',
            'preparing',
            'ready',
        ]);
    } elseif ($status && $status !== 'all') {
        $ordersQuery->where('status', $status);
    }

    $orders = $ordersQuery
        ->limit(80)
        ->get();

    $serviceRequests = $workspace->restaurantTableServiceRequests()
        ->with([
            'branch:id,name',
            'table:id,name,number',
        ])
        ->whereIn('status', ['new', 'seen'])
        ->latest('id')
        ->limit(80)
        ->get();

    $cards = collect();

    foreach ($orders as $order) {
        $cards->push([
            'kind' => 'order',
            'created_at' => $order->created_at,
            'model' => $order,
        ]);
    }

    foreach ($serviceRequests as $serviceRequest) {
        $cards->push([
            'kind' => 'service_request',
            'created_at' => $serviceRequest->created_at,
            'model' => $serviceRequest,
        ]);
    }

    $cards = $cards
        ->sortByDesc('created_at')
        ->values();

    $counts = [
        'active' => $workspace->restaurantOrders()
            ->whereIn('status', ['new', 'accepted', 'preparing', 'ready'])
            ->count(),

        'new' => $workspace->restaurantOrders()
            ->where('status', 'new')
            ->count(),

        'preparing' => $workspace->restaurantOrders()
            ->where('status', 'preparing')
            ->count(),

        'ready' => $workspace->restaurantOrders()
            ->where('status', 'ready')
            ->count(),

        'completed' => $workspace->restaurantOrders()
            ->where('status', 'completed')
            ->count(),

        'service_requests' => $workspace->restaurantTableServiceRequests()
            ->whereIn('status', ['new', 'seen'])
            ->count(),
    ];

      return view(
        'app.restaurant-menu.orders.index',
        $this->operationsPayload($request, $workspace)
    );

    return view('app.restaurant-menu.orders.index', compact(
        'workspace',
        'cards',
        'counts',
        'status'
    ));
}


public function live(Request $request, Workspace $workspace)
{
    $payload = $this->operationsPayload($request, $workspace);

    return response()->json([
        'html' => view('app.restaurant-menu.orders.partials.cards', $payload)->render(),
        'counts' => $payload['counts'],
        'last_event_id' => $payload['lastEventId'],
        'server_time' => now()->format('H:i:s'),
    ]);
}

    public function show(Workspace $workspace, RestaurantOrder $restaurantOrder)
    {
        $this->ensureOrderBelongsToWorkspace($workspace, $restaurantOrder);

        $restaurantOrder->load([
    'branch',
    'invoice',
    'items.options',
]);

        return view('app.restaurant-menu.orders.show', compact(
            'workspace',
            'restaurantOrder'
        ));
    }

  public function updateStatus(Request $request, Workspace $workspace, RestaurantOrder $restaurantOrder)
{
    $this->ensureOrderBelongsToWorkspace($workspace, $restaurantOrder);

    $data = $request->validate([
        'status' => [
            'required',
            Rule::in(['new', 'accepted', 'preparing', 'ready', 'completed', 'cancelled']),
        ],
    ]);

    $this->restaurantOrderService->updateStatus(
        order: $restaurantOrder,
        status: $data['status']
    );

    return back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
}

    private function ensureOrderBelongsToWorkspace(Workspace $workspace, RestaurantOrder $restaurantOrder): void
    {
        abort_if((int) $restaurantOrder->workspace_id !== (int) $workspace->id, 404);
    }




    



    private function operationsPayload(Request $request, Workspace $workspace): array
{
    $status = $request->input('status', 'active');

    $ordersQuery = $workspace->restaurantOrders()
        ->with([
            'branch:id,name',
            'items',
        ])
        ->latest('id');

    if ($status === 'active') {
        $ordersQuery->whereIn('status', [
            'new',
            'accepted',
            'preparing',
            'ready',
        ]);
    } elseif ($status && $status !== 'all') {
        $ordersQuery->where('status', $status);
    }

    $orders = $ordersQuery
        ->limit(80)
        ->get();

    $serviceRequests = $workspace->restaurantTableServiceRequests()
        ->with([
            'branch:id,name',
            'table:id,name,number',
        ])
        ->whereIn('status', ['new', 'seen'])
        ->latest('id')
        ->limit(80)
        ->get();

    $cards = collect();

    foreach ($orders as $order) {
        $cards->push([
            'kind' => 'order',
            'created_at' => $order->created_at,
            'model' => $order,
            'id' => 'order-' . $order->id,
        ]);
    }

    foreach ($serviceRequests as $serviceRequest) {
        $cards->push([
            'kind' => 'service_request',
            'created_at' => $serviceRequest->created_at,
            'model' => $serviceRequest,
            'id' => 'service-' . $serviceRequest->id,
        ]);
    }

    $cards = $cards
        ->sortByDesc('created_at')
        ->values();

    $counts = [
        'active' => $workspace->restaurantOrders()
            ->whereIn('status', ['new', 'accepted', 'preparing', 'ready'])
            ->count(),

        'new' => $workspace->restaurantOrders()
            ->where('status', 'new')
            ->count(),

        'preparing' => $workspace->restaurantOrders()
            ->where('status', 'preparing')
            ->count(),

        'ready' => $workspace->restaurantOrders()
            ->where('status', 'ready')
            ->count(),

        'completed' => $workspace->restaurantOrders()
            ->where('status', 'completed')
            ->count(),

        'service_requests' => $workspace->restaurantTableServiceRequests()
            ->whereIn('status', ['new', 'seen'])
            ->count(),
    ];

    $lastEventId = $cards->first()['id'] ?? null;

    return compact(
        'workspace',
        'cards',
        'counts',
        'status',
        'lastEventId'
    );
}

public function receipt(Workspace $workspace, RestaurantOrder $restaurantOrder)
{
    $this->ensureOrderBelongsToWorkspace($workspace, $restaurantOrder);

    $restaurantOrder->load([
        'branch',
        'invoice',
        'items.options',
    ]);

    $profile = $workspace->businessProfile;

    return view('app.restaurant-menu.orders.receipt', compact(
        'workspace',
        'restaurantOrder',
        'profile'
    ));
}
}