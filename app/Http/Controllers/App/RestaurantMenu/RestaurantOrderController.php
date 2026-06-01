<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Models\RestaurantMenu\RestaurantItemOption;
use App\Models\RestaurantMenu\RestaurantMenuItem;
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
    'deliveryZone',
    'deliveryCourier',
    'events.user',
]);


$menuItems = $workspace->restaurantMenuItems()
    ->with([
        'category:id,name',
        'variants' => function ($query) {
            $query->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id');
        },
        'optionGroups' => function ($query) {
            $query->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id');
        },
        'optionGroups.options' => function ($query) {
            $query->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id');
        },
    ])
    ->where('branch_id', $restaurantOrder->branch_id)
    ->where('is_available', true)
    ->orderBy('name')
    ->get();

$menuItemsPayload = $menuItems->map(function ($item) {
    return [
        'id' => $item->id,
        'name' => $item->name,
        'description' => $item->description,
        'price' => (float) $item->price,
        'currency' => $item->currency ?? 'EGP',
        'category_name' => $item->category?->name,

        'variants' => $item->variants->map(function ($variant) {
            return [
                'id' => $variant->id,
                'name' => $variant->name,
                'price' => (float) $variant->price,
                'is_default' => (bool) ($variant->is_default ?? false),
            ];
        })->values(),

        'option_groups' => $item->optionGroups->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'type' => $group->type,
                'is_required' => (bool) $group->is_required,
                'min_choices' => $group->min_choices,
                'max_choices' => $group->max_choices,

                'options' => $group->options->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'name' => $option->name,
                        'price' => (float) $option->price,
                    ];
                })->values(),
            ];
        })->values(),
    ];
})->values();

    $deliveryCouriers = $workspace->activeRestaurantDeliveryCouriers()
        ->where(function ($query) use ($restaurantOrder) {
            $query->whereNull('branch_id');

            if ($restaurantOrder->branch_id) {
                $query->orWhere('branch_id', $restaurantOrder->branch_id);
            }
        })
        ->get();



        return view('app.restaurant-menu.orders.show', compact(
            'workspace',
            'restaurantOrder',
        'deliveryCouriers',

        'menuItems',
'menuItemsPayload',
        ));
    }


    public function storeItem(Request $request, Workspace $workspace, RestaurantOrder $restaurantOrder)
{
    $this->ensureOrderBelongsToWorkspace($workspace, $restaurantOrder);

    if (in_array($restaurantOrder->status, ['completed', 'cancelled'], true)) {
        return back()->with('error', 'لا يمكن إضافة صنف إلى طلب مكتمل أو ملغي.');
    }

    $data = $request->validate([
        'item_id' => [
            'required',
            'integer',
            Rule::exists('restaurant_menu_items', 'id')
                ->where('workspace_id', $workspace->id)
                ->where('branch_id', $restaurantOrder->branch_id)
                ->where('is_available', 1),
        ],

        'variant_id' => ['nullable', 'integer'],
        'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        'options' => ['nullable', 'array'],
        'options.*' => ['integer'],
        'notes' => ['nullable', 'string', 'max:1000'],
        'edit_reason' => ['required', 'string', 'max:500'],
    ]);

    $restaurantOrder->load('items');

    $oldValues = [
        'subtotal' => $restaurantOrder->subtotal,
        'delivery_fee' => $restaurantOrder->delivery_fee,
        'tax_total' => $restaurantOrder->tax_total,
        'discount_total' => $restaurantOrder->discount_total,
        'total' => $restaurantOrder->total,
        'items_count' => $restaurantOrder->items->count(),
    ];

    \Illuminate\Support\Facades\DB::transaction(function () use ($workspace, $restaurantOrder, $data, $oldValues) {
        $item = RestaurantMenuItem::query()
            ->with([
                'variants',
                'optionGroups.options',
            ])
            ->where('workspace_id', $workspace->id)
            ->where('branch_id', $restaurantOrder->branch_id)
            ->whereKey($data['item_id'])
            ->where('is_available', true)
            ->firstOrFail();

        $variant = null;

        if (! empty($data['variant_id'])) {
            $variant = $item->variants()
                ->whereKey($data['variant_id'])
                ->where('is_active', true)
                ->firstOrFail();
        }

        $selectedOptions = collect();

        if (! empty($data['options'])) {
            $selectedOptions = RestaurantItemOption::query()
                ->whereIn('id', $data['options'])
                ->where('workspace_id', $workspace->id)
                ->where('branch_id', $restaurantOrder->branch_id)
                ->where('is_active', true)
                ->get();
        }

        $unitPrice = $variant
            ? (float) $variant->price
            : (float) $item->price;

        foreach ($selectedOptions as $option) {
            $unitPrice += (float) $option->price;
        }

        $quantity = (int) $data['quantity'];
        $lineTotal = $unitPrice * $quantity;

        /*
         * هنا نحتاج معرفة أسماء أعمدة جدول restaurant_order_items عندك.
         * الكود التالي يحاول يدعم line_total أو total حسب الموجود.
         */
        $orderItemPayload = [
            'workspace_id' => $workspace->id,
            'branch_id' => $restaurantOrder->branch_id,
            'restaurant_order_id' => $restaurantOrder->id,

            'item_id' => $item->id,
            'variant_id' => $variant?->id,

            'name' => $item->name,
            'item_name' => $item->name,
            'variant_name' => $variant?->name,

            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'notes' => $data['notes'] ?? null,
        ];

        $orderItem = new \App\Models\RestaurantMenu\RestaurantOrderItem();

        if (\Illuminate\Support\Facades\Schema::hasColumn($orderItem->getTable(), 'line_total')) {
            $orderItemPayload['line_total'] = $lineTotal;
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn($orderItem->getTable(), 'total')) {
            $orderItemPayload['total'] = $lineTotal;
        }

        /*
         * حذف الحقول غير الموجودة حتى لا يحصل SQL error
         */
        $table = $orderItem->getTable();

        $orderItemPayload = collect($orderItemPayload)
            ->filter(function ($value, $key) use ($table) {
                return \Illuminate\Support\Facades\Schema::hasColumn($table, $key);
            })
            ->all();

        $orderItem = $restaurantOrder->items()->create($orderItemPayload);

        foreach ($selectedOptions as $option) {
            $optionPayload = [
                'workspace_id' => $workspace->id,
                'branch_id' => $restaurantOrder->branch_id,
                'restaurant_order_item_id' => $orderItem->id,
                'option_id' => $option->id,
                'name' => $option->name,
                'price' => (float) $option->price,
            ];

            $optionModel = new \App\Models\RestaurantMenu\RestaurantOrderItemOption();
            $optionTable = $optionModel->getTable();

            $optionPayload = collect($optionPayload)
                ->filter(function ($value, $key) use ($optionTable) {
                    return \Illuminate\Support\Facades\Schema::hasColumn($optionTable, $key);
                })
                ->all();

            $orderItem->options()->create($optionPayload);
        }

        $restaurantOrder->refresh();
        $restaurantOrder->load('items');

        $subtotal = $restaurantOrder->items->sum(function ($orderItem) {
            return (float) ($orderItem->line_total ?? $orderItem->total ?? 0);
        });

        $discountTotal = (float) ($restaurantOrder->discount_total ?? 0);
        $taxTotal = (float) ($restaurantOrder->tax_total ?? 0);
        $deliveryFee = (float) ($restaurantOrder->delivery_fee ?? 0);

        $total = $subtotal - $discountTotal + $taxTotal;

        if (
            $restaurantOrder->order_type === 'delivery'
            && (bool) $restaurantOrder->delivery_fee_included_in_total
        ) {
            $total += $deliveryFee;
        }

        // $restaurantOrder->update([
        //     'subtotal' => $subtotal,
        //     'total' => $total,
        // ]);
        $this->recalculateOrderTotals($restaurantOrder);

$restaurantOrder->refresh();

        $restaurantOrder->events()->create([
            'workspace_id' => $workspace->id,
            'branch_id' => $restaurantOrder->branch_id,
            'user_id' => auth()->id(),
            'event_type' => 'edited',
            'old_values' => $oldValues,
            'new_values' => [
                'action' => 'item_added',
                'item_id' => $item->id,
                'item_name' => $item->name,
                'variant_id' => $variant?->id,
                'variant_name' => $variant?->name,
                'options' => $selectedOptions->map(fn ($option) => [
                    'id' => $option->id,
                    'name' => $option->name,
                    'price' => (float) $option->price,
                ])->values(),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
                'subtotal' => $subtotal,
                'total' => $total,
            ],
            'reason' => $data['edit_reason'],
            'created_at' => now(),
        ]);
    });

    return redirect()
        ->route('app.restaurant-menu.orders.show', [$workspace, $restaurantOrder])
        ->with('success', 'تمت إضافة الصنف إلى الطلب.');
}


    public function updateStatus(Request $request, Workspace $workspace, RestaurantOrder $restaurantOrder)
{
    $this->ensureOrderBelongsToWorkspace($workspace, $restaurantOrder);

    $data = $request->validate([
        'status' => [
            'required',
            Rule::in(['new', 'accepted', 'preparing', 'ready', 'completed', 'cancelled']),
        ],

        'print' => [
            'nullable',
            Rule::in(['kitchen', 'client', 'both']),
        ],
    ]);

    $this->restaurantOrderService->updateStatus(
        order: $restaurantOrder,
        status: $data['status']
    );

    if (! empty($data['print'])) {
        return redirect()->route('app.restaurant-menu.orders.receipt', [
            $workspace,
            $restaurantOrder,
            'type' => $data['print'],
            'auto_print' => 1,
        ]);
    }

    return back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
}
  public function updateStatusX(Request $request, Workspace $workspace, RestaurantOrder $restaurantOrder)
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






    public function cancel(Request $request, Workspace $workspace, RestaurantOrder $restaurantOrder)
{
    $this->ensureOrderBelongsToWorkspace($workspace, $restaurantOrder);

    $data = $request->validate([
        'reason' => ['required', 'string', 'max:500'],
    ]);

    if (in_array($restaurantOrder->status, ['completed', 'cancelled'], true)) {
        return back()->with('error', 'لا يمكن إلغاء طلب مكتمل أو ملغي بالفعل.');
    }

    $old = [
        'status' => $restaurantOrder->status,
        'delivery_status' => $restaurantOrder->delivery_status,
        'total' => $restaurantOrder->total,
    ];

    $restaurantOrder->update([
        'status' => 'cancelled',
    ]);

    $restaurantOrder->events()->create([
        'workspace_id' => $workspace->id,
        'branch_id' => $restaurantOrder->branch_id,
        'user_id' => auth()->id(),
        'event_type' => 'cancelled',
        'old_values' => $old,
        'new_values' => [
            'status' => 'cancelled',
        ],
        'reason' => $data['reason'],
        'created_at' => now(),
    ]);

    return redirect()
        ->route('app.restaurant-menu.orders.show', [$workspace, $restaurantOrder])
        ->with('success', 'تم إلغاء الطلب.');
}



public function edit(Workspace $workspace, RestaurantOrder $restaurantOrder)
{
    $this->ensureOrderBelongsToWorkspace($workspace, $restaurantOrder);

    abort_if(
        in_array($restaurantOrder->status, ['completed', 'cancelled'], true),
        403,
        'لا يمكن تعديل طلب مكتمل أو ملغي.'
    );

    $restaurantOrder->load([
        'branch',
        'deliveryZone',
        'deliveryCourier',
        'items.options',
    ]);

    $branches = $workspace->restaurantBranches()
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get(['id', 'name']);

    $deliveryZones = $workspace->activeRestaurantDeliveryZones()
        ->where(function ($query) use ($restaurantOrder) {
            $query->whereNull('branch_id');

            if ($restaurantOrder->branch_id) {
                $query->orWhere('branch_id', $restaurantOrder->branch_id);
            }
        })
        ->get();

    $deliveryCouriers = $workspace->activeRestaurantDeliveryCouriers()
        ->where(function ($query) use ($restaurantOrder) {
            $query->whereNull('branch_id');

            if ($restaurantOrder->branch_id) {
                $query->orWhere('branch_id', $restaurantOrder->branch_id);
            }
        })
        ->get();

    return view('app.restaurant-menu.orders.edit', compact(
        'workspace',
        'restaurantOrder',
        'branches',
        'deliveryZones',
        'deliveryCouriers'
    ));
}

public function update(Request $request, Workspace $workspace, RestaurantOrder $restaurantOrder)
{
    $this->ensureOrderBelongsToWorkspace($workspace, $restaurantOrder);

    if (in_array($restaurantOrder->status, ['completed', 'cancelled'], true)) {
        return back()->with('error', 'لا يمكن تعديل طلب مكتمل أو ملغي.');
    }

    $data = $request->validate([
        'customer_name' => ['required', 'string', 'max:150'],
        'customer_phone' => ['required', 'string', 'max:50'],
        'customer_email' => ['nullable', 'email', 'max:190'],

        'order_type' => ['required', Rule::in(['dine_in', 'takeaway', 'delivery'])],
        'table_number' => ['nullable', 'string', 'max:50'],

        'delivery_zone_id' => ['nullable', 'integer'],
        'delivery_address' => ['nullable', 'string', 'max:3000'],
        'delivery_address_details' => ['nullable', 'string', 'max:3000'],
        'delivery_area' => ['nullable', 'string', 'max:150'],
        'delivery_building' => ['nullable', 'string', 'max:100'],
        'delivery_floor' => ['nullable', 'string', 'max:100'],
        'delivery_apartment' => ['nullable', 'string', 'max:100'],
        'delivery_landmark' => ['nullable', 'string', 'max:255'],

        'delivery_courier_id' => ['nullable', 'integer'],
        'delivery_courier_name' => ['nullable', 'string', 'max:150'],
        'delivery_courier_phone' => ['nullable', 'string', 'max:50'],
        'delivery_company_name' => ['nullable', 'string', 'max:150'],
        'delivery_status' => [
            'nullable',
            Rule::in([
                'not_assigned',
                'assigned',
                'picked_up',
                'on_the_way',
                'delivered',
                'failed',
            ]),
        ],

        'notes' => ['nullable', 'string', 'max:3000'],
        'edit_reason' => ['required', 'string', 'max:500'],
    ]);

    $deliveryZone = null;
    $deliveryCourier = null;

    if ($data['order_type'] === 'delivery' && ! empty($data['delivery_zone_id'])) {
        $deliveryZone = $workspace->activeRestaurantDeliveryZones()
            ->where(function ($query) use ($restaurantOrder) {
                $query->whereNull('branch_id');

                if ($restaurantOrder->branch_id) {
                    $query->orWhere('branch_id', $restaurantOrder->branch_id);
                }
            })
            ->whereKey($data['delivery_zone_id'])
            ->first();
    }

    if ($data['order_type'] === 'delivery' && ! empty($data['delivery_courier_id'])) {
        $deliveryCourier = $workspace->activeRestaurantDeliveryCouriers()
            ->where(function ($query) use ($restaurantOrder) {
                $query->whereNull('branch_id');

                if ($restaurantOrder->branch_id) {
                    $query->orWhere('branch_id', $restaurantOrder->branch_id);
                }
            })
            ->whereKey($data['delivery_courier_id'])
            ->first();
    }

    if ($data['order_type'] === 'delivery' && empty($data['delivery_address_details']) && empty($data['delivery_address'])) {
        return back()
            ->withInput()
            ->with('error', 'عنوان التوصيل مطلوب لطلبات الدليفري.');
    }

    $oldValues = $restaurantOrder->only([
        'customer_name',
        'customer_phone',
        'customer_email',
        'order_type',
        'table_number',
        'delivery_zone_id',
        'delivery_address',
        'delivery_address_details',
        'delivery_area',
        'delivery_building',
        'delivery_floor',
        'delivery_apartment',
        'delivery_landmark',
        'delivery_courier_id',
        'delivery_courier_name',
        'delivery_courier_phone',
        'delivery_company_name',
        'delivery_status',
        'notes',
    ]);

    $payload = [
        'customer_name' => $data['customer_name'],
        'customer_phone' => $data['customer_phone'],
        'customer_email' => $data['customer_email'] ?? null,

        'order_type' => $data['order_type'],
        'table_number' => $data['order_type'] === 'dine_in'
            ? ($data['table_number'] ?? null)
            : null,

        'notes' => $data['notes'] ?? null,
    ];

    if ($data['order_type'] === 'delivery') {
        $payload = array_merge($payload, [
            'delivery_zone_id' => $deliveryZone?->id,
            'delivery_address' => $data['delivery_address'] ?? $data['delivery_address_details'] ?? null,
            'delivery_address_details' => $data['delivery_address_details'] ?? null,
            'delivery_area' => $data['delivery_area'] ?? null,
            'delivery_building' => $data['delivery_building'] ?? null,
            'delivery_floor' => $data['delivery_floor'] ?? null,
            'delivery_apartment' => $data['delivery_apartment'] ?? null,
            'delivery_landmark' => $data['delivery_landmark'] ?? null,

            'delivery_courier_id' => $deliveryCourier?->id,
            'delivery_courier_name' => $deliveryCourier?->name ?: ($data['delivery_courier_name'] ?? null),
            'delivery_courier_phone' => $deliveryCourier?->phone ?: ($data['delivery_courier_phone'] ?? null),
            'delivery_company_name' => $deliveryCourier?->company_name ?: ($data['delivery_company_name'] ?? null),
            'delivery_status' => $data['delivery_status'] ?? $restaurantOrder->delivery_status ?? 'not_assigned',
        ]);
    } else {
        $payload = array_merge($payload, [
            'delivery_zone_id' => null,
            'delivery_address' => null,
            'delivery_address_details' => null,
            'delivery_area' => null,
            'delivery_building' => null,
            'delivery_floor' => null,
            'delivery_apartment' => null,
            'delivery_landmark' => null,

            'delivery_courier_id' => null,
            'delivery_courier_name' => null,
            'delivery_courier_phone' => null,
            'delivery_company_name' => null,
            'delivery_status' => 'not_assigned',
        ]);
    }

    $restaurantOrder->update($payload);

    $restaurantOrder->events()->create([
        'workspace_id' => $workspace->id,
        'branch_id' => $restaurantOrder->branch_id,
        'user_id' => auth()->id(),
        'event_type' => 'edited',
        'old_values' => $oldValues,
        'new_values' => $payload,
        'reason' => $data['edit_reason'],
        'created_at' => now(),
    ]);

    return redirect()
        ->route('app.restaurant-menu.orders.show', [$workspace, $restaurantOrder])
        ->with('success', 'تم تعديل بيانات الطلب.');
}


public function updateItems(Request $request, Workspace $workspace, RestaurantOrder $restaurantOrder)
{
    $this->ensureOrderBelongsToWorkspace($workspace, $restaurantOrder);

    if (in_array($restaurantOrder->status, ['completed', 'cancelled'], true)) {
        return back()->with('error', 'لا يمكن تعديل أصناف طلب مكتمل أو ملغي.');
    }

    $data = $request->validate([
        'items' => ['required', 'array', 'min:1'],

        'items.*.id' => ['required', 'integer'],
        'items.*.quantity' => ['required', 'integer', 'min:0', 'max:999'],
        'items.*.variant_id' => ['nullable', 'integer'],
        'items.*.options' => ['nullable', 'array'],
        'items.*.options.*' => ['integer'],
        'items.*.notes' => ['nullable', 'string', 'max:1000'],

        'edit_reason' => ['required', 'string', 'max:500'],
    ]);

    $restaurantOrder->load([
        'items.options',
    ]);

    $oldValues = [
        'subtotal' => $restaurantOrder->subtotal,
        'delivery_fee' => $restaurantOrder->delivery_fee,
        'tax_total' => $restaurantOrder->tax_total,
        'discount_total' => $restaurantOrder->discount_total,
        'total' => $restaurantOrder->total,
        'items' => $restaurantOrder->items->map(function ($item) {
            return [
                'id' => $item->id,
                'item_id' => $item->item_id ?? null,
                'variant_id' => $item->variant_id ?? null,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'line_total' => $item->line_total ?? $item->total ?? null,
                'notes' => $item->notes,
                'options' => $item->options?->map(fn ($option) => [
                    'id' => $option->option_id ?? $option->id,
                    'name' => $option->name,
                    'price' => $option->price,
                ])->values(),
            ];
        })->values(),
    ];

    \Illuminate\Support\Facades\DB::transaction(function () use ($workspace, $restaurantOrder, $data, $oldValues) {
        $itemsPayload = collect($data['items'])->keyBy('id');

        foreach ($restaurantOrder->items as $orderItem) {
            $payload = $itemsPayload->get($orderItem->id);

            if (! $payload) {
                continue;
            }

            $quantity = (int) $payload['quantity'];

            /*
             * Remove line
             */
            if ($quantity <= 0) {
                $orderItem->options()->delete();
                $orderItem->delete();
                continue;
            }

            /*
             * Offers: لا نعدل variants/options لها الآن
             */
            if (! empty($orderItem->offer_id)) {
                $unitPrice = (float) $orderItem->unit_price;
                $lineTotal = $unitPrice * $quantity;

                $updatePayload = [
                    'quantity' => $quantity,
                    'notes' => $payload['notes'] ?? null,
                ];

                $this->fillLineTotalColumns($orderItem, $updatePayload, $lineTotal);

                $orderItem->update($updatePayload);

                continue;
            }

            /*
             * Normal item
             */
            $menuItem = RestaurantMenuItem::query()
                ->with([
                    'variants',
                    'optionGroups.options',
                ])
                ->where('workspace_id', $workspace->id)
                ->where('branch_id', $restaurantOrder->branch_id)
                ->whereKey($orderItem->item_id)
                ->firstOrFail();

            $variant = null;

            if (! empty($payload['variant_id'])) {
                $variant = $menuItem->variants()
                    ->whereKey($payload['variant_id'])
                    ->where('is_active', true)
                    ->firstOrFail();
            }

            $selectedOptions = collect();

            if (! empty($payload['options'])) {
                $selectedOptions = RestaurantItemOption::query()
                    ->whereIn('id', $payload['options'])
                    ->where('workspace_id', $workspace->id)
                    ->where('branch_id', $restaurantOrder->branch_id)
                    ->where('is_active', true)
                    ->get();
            }

            /*
             * Validate required option groups
             */
            foreach ($menuItem->optionGroups as $group) {
                if (! $group->is_required) {
                    continue;
                }

                $selectedCount = $selectedOptions
                    ->filter(fn ($option) => (int) $option->group_id === (int) $group->id)
                    ->count();

                if ($selectedCount < 1) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' => 'يجب اختيار إضافة من مجموعة: ' . $group->name,
                    ]);
                }
            }

            /*
             * Validate max choices
             */
            foreach ($menuItem->optionGroups as $group) {
                if (! $group->max_choices) {
                    continue;
                }

                $selectedCount = $selectedOptions
                    ->filter(fn ($option) => (int) $option->group_id === (int) $group->id)
                    ->count();

                if ($selectedCount > (int) $group->max_choices) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'items' => 'تم اختيار إضافات أكثر من المسموح في مجموعة: ' . $group->name,
                    ]);
                }
            }

            $unitPrice = $variant
                ? (float) $variant->price
                : (float) $menuItem->price;

            foreach ($selectedOptions as $option) {
                $unitPrice += (float) $option->price;
            }

            $lineTotal = $unitPrice * $quantity;

            $updatePayload = [
                'variant_id' => $variant?->id,
                'variant_name' => $variant?->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'notes' => $payload['notes'] ?? null,
            ];

            $this->fillLineTotalColumns($orderItem, $updatePayload, $lineTotal);

            /*
             * احذف أي مفاتيح غير موجودة في جدول order items
             */
            $table = $orderItem->getTable();

            $updatePayload = collect($updatePayload)
                ->filter(fn ($value, $key) => \Illuminate\Support\Facades\Schema::hasColumn($table, $key))
                ->all();

            $orderItem->update($updatePayload);

            /*
             * Rebuild options
             */
            $orderItem->options()->delete();

            foreach ($selectedOptions as $option) {
                $optionPayload = [
                    'workspace_id' => $workspace->id,
                    'branch_id' => $restaurantOrder->branch_id,
                    'restaurant_order_item_id' => $orderItem->id,
                    'option_id' => $option->id,
                    'name' => $option->name,
                    'price' => (float) $option->price,
                ];

                $optionModel = new \App\Models\RestaurantMenu\RestaurantOrderItemOption();
                $optionTable = $optionModel->getTable();

                $optionPayload = collect($optionPayload)
                    ->filter(fn ($value, $key) => \Illuminate\Support\Facades\Schema::hasColumn($optionTable, $key))
                    ->all();

                $orderItem->options()->create($optionPayload);
            }
        }

        $this->recalculateOrderTotals($restaurantOrder);

        $restaurantOrder->refresh();
        $restaurantOrder->load('items.options');

        $restaurantOrder->events()->create([
            'workspace_id' => $workspace->id,
            'branch_id' => $restaurantOrder->branch_id,
            'user_id' => auth()->id(),
            'event_type' => 'edited',
            'old_values' => $oldValues,
            'new_values' => [
                'action' => 'items_updated',
                'subtotal' => $restaurantOrder->subtotal,
                'delivery_fee' => $restaurantOrder->delivery_fee,
                'tax_total' => $restaurantOrder->tax_total,
                'discount_total' => $restaurantOrder->discount_total,
                'total' => $restaurantOrder->total,
                'items' => $restaurantOrder->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'item_id' => $item->item_id ?? null,
                        'variant_id' => $item->variant_id ?? null,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'line_total' => $item->line_total ?? $item->total ?? null,
                        'notes' => $item->notes,
                        'options' => $item->options?->map(fn ($option) => [
                            'id' => $option->option_id ?? $option->id,
                            'name' => $option->name,
                            'price' => $option->price,
                        ])->values(),
                    ];
                })->values(),
            ],
            'reason' => $data['edit_reason'],
            'created_at' => now(),
        ]);
    });

    return redirect()
        ->route('app.restaurant-menu.orders.show', [$workspace, $restaurantOrder])
        ->with('success', 'تم تعديل أصناف الطلب.');
}
public function updateItemsX(Request $request, Workspace $workspace, RestaurantOrder $restaurantOrder)
{
    $this->ensureOrderBelongsToWorkspace($workspace, $restaurantOrder);

    if (in_array($restaurantOrder->status, ['completed', 'cancelled'], true)) {
        return back()->with('error', 'لا يمكن تعديل أصناف طلب مكتمل أو ملغي.');
    }

    $data = $request->validate([
        'items' => ['required', 'array', 'min:1'],

        'items.*.id' => ['required', 'integer'],
        'items.*.quantity' => ['required', 'integer', 'min:0', 'max:999'],
        'items.*.notes' => ['nullable', 'string', 'max:1000'],

        'edit_reason' => ['required', 'string', 'max:500'],
    ]);

    $restaurantOrder->load('items');

    $oldValues = [
        'subtotal' => $restaurantOrder->subtotal,
        'delivery_fee' => $restaurantOrder->delivery_fee,
        'tax_total' => $restaurantOrder->tax_total,
        'discount_total' => $restaurantOrder->discount_total,
        'total' => $restaurantOrder->total,
        'items' => $restaurantOrder->items->map(function ($item) {
            return [
                'id' => $item->id,
                'item_id' => $item->item_id ?? null,
                'offer_id' => $item->offer_id ?? null,
                'name' => $item->name ?? $item->item_name ?? null,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total ?? $item->line_total ?? null,
                'notes' => $item->notes,
            ];
        })->values(),
    ];

    \Illuminate\Support\Facades\DB::transaction(function () use ($restaurantOrder, $workspace, $data, $oldValues) {
        $itemsPayload = collect($data['items'])->keyBy('id');

        foreach ($restaurantOrder->items as $orderItem) {
            $payload = $itemsPayload->get($orderItem->id);

            if (! $payload) {
                continue;
            }

            $quantity = (int) $payload['quantity'];

            if ($quantity <= 0) {
                $orderItem->delete();
                continue;
            }

            $unitPrice = (float) $orderItem->unit_price;
            $lineTotal = $unitPrice * $quantity;

            $updatePayload = [
                'quantity' => $quantity,
                'notes' => $payload['notes'] ?? null,
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn($orderItem->getTable(), 'line_total')) {
                $updatePayload['line_total'] = $lineTotal;
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn($orderItem->getTable(), 'total')) {
                $updatePayload['total'] = $lineTotal;
            }

            $orderItem->update($updatePayload);
        }

        $restaurantOrder->refresh();
        $restaurantOrder->load('items');

        if ($restaurantOrder->items->count() < 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'items' => 'لا يمكن ترك الطلب بدون أصناف.',
            ]);
        }

        $subtotal = $restaurantOrder->items->sum(function ($item) {
            return (float) ($item->line_total ?? $item->total ?? 0);
        });

        $discountTotal = (float) ($restaurantOrder->discount_total ?? 0);
        $taxTotal = (float) ($restaurantOrder->tax_total ?? 0);
        $deliveryFee = (float) ($restaurantOrder->delivery_fee ?? 0);

        $total = $subtotal - $discountTotal + $taxTotal;

        if (
            $restaurantOrder->order_type === 'delivery'
            && (bool) $restaurantOrder->delivery_fee_included_in_total
        ) {
            $total += $deliveryFee;
        }

        $restaurantOrder->update([
            'subtotal' => $subtotal,
            'total' => $total,
        ]);

        $restaurantOrder->events()->create([
            'workspace_id' => $workspace->id,
            'branch_id' => $restaurantOrder->branch_id,
            'user_id' => auth()->id(),
            'event_type' => 'edited',
            'old_values' => $oldValues,
            'new_values' => [
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'tax_total' => $taxTotal,
                'discount_total' => $discountTotal,
                'total' => $total,
                'items' => $restaurantOrder->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total' => $item->line_total ?? $item->total ?? null,
                        'notes' => $item->notes,
                    ];
                })->values(),
            ],
            'reason' => $data['edit_reason'],
            'created_at' => now(),
        ]);
    });

    return redirect()
        ->route('app.restaurant-menu.orders.show', [$workspace, $restaurantOrder])
        ->with('success', 'تم تعديل أصناف الطلب وإعادة حساب الإجمالي.');
}


private function fillLineTotalColumns($orderItem, array &$payload, float $lineTotal): void
{
    $table = $orderItem->getTable();

    if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'line_total')) {
        $payload['line_total'] = $lineTotal;
    }

    if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'total')) {
        $payload['total'] = $lineTotal;
    }
}

private function recalculateOrderTotals(RestaurantOrder $restaurantOrder): void
{
    $restaurantOrder->refresh();
    $restaurantOrder->load('items');

    if ($restaurantOrder->items->count() < 1) {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'items' => 'لا يمكن ترك الطلب بدون أصناف.',
        ]);
    }

    $subtotal = $restaurantOrder->items->sum(function ($item) {
        return (float) ($item->line_total ?? $item->total ?? 0);
    });

    $discountTotal = (float) ($restaurantOrder->discount_total ?? 0);
    $taxTotal = (float) ($restaurantOrder->tax_total ?? 0);
    $deliveryFee = (float) ($restaurantOrder->delivery_fee ?? 0);

    $total = $subtotal - $discountTotal + $taxTotal;

    if (
        $restaurantOrder->order_type === 'delivery'
        && (bool) $restaurantOrder->delivery_fee_included_in_total
    ) {
        $total += $deliveryFee;
    }

    $restaurantOrder->update([
        'subtotal' => $subtotal,
        'total' => $total,
    ]);
}





    public function updateDelivery(Request $request, Workspace $workspace, RestaurantOrder $restaurantOrder)
{
    $this->ensureOrderBelongsToWorkspace($workspace, $restaurantOrder);

    abort_if($restaurantOrder->order_type !== 'delivery', 404);

    $data = $request->validate([
        'delivery_courier_id' => [
            'nullable',
            'integer',
            Rule::exists('restaurant_delivery_couriers', 'id')
                ->where('workspace_id', $workspace->id)
                ->where('is_active', 1),
        ],

        'delivery_courier_name' => ['nullable', 'string', 'max:150'],
        'delivery_courier_phone' => ['nullable', 'string', 'max:50'],
        'delivery_company_name' => ['nullable', 'string', 'max:150'],

        'delivery_status' => [
            'required',
            Rule::in([
                'not_assigned',
                'assigned',
                'picked_up',
                'on_the_way',
                'delivered',
                'failed',
            ]),
        ],
    ]);

    $courier = null;

    if (! empty($data['delivery_courier_id'])) {
        $courier = $workspace->restaurantDeliveryCouriers()
            ->whereKey($data['delivery_courier_id'])
            ->where('is_active', true)
            ->where(function ($query) use ($restaurantOrder) {
                $query->whereNull('branch_id');

                if ($restaurantOrder->branch_id) {
                    $query->orWhere('branch_id', $restaurantOrder->branch_id);
                }
            })
            ->firstOrFail();
    }

    $payload = [
        'delivery_courier_id' => $courier?->id,
        'delivery_courier_name' => $courier?->name ?: ($data['delivery_courier_name'] ?? null),
        'delivery_courier_phone' => $courier?->phone ?: ($data['delivery_courier_phone'] ?? null),
        'delivery_company_name' => $courier?->company_name ?: ($data['delivery_company_name'] ?? null),
        'delivery_status' => $data['delivery_status'],
    ];

    /*
     * عند اختيار دليفري موجود، نخزن snapshot للاسم والهاتف
     * حتى لو بيانات الدليفري اتعدلت لاحقًا.
     */
    if ($courier) {
        $payload['delivery_courier_name'] = $courier->name;
        $payload['delivery_courier_phone'] = $courier->phone;
        $payload['delivery_company_name'] = $courier->company_name;
    }

    /*
     * لو تم التسليم، نخلي الطلب completed.
     * ممكن لاحقًا نخليها إعداد اختياري.
     */
    if ($data['delivery_status'] === 'delivered') {
        $payload['status'] = 'completed';
    }

    $restaurantOrder->update($payload);

    return back()->with('success', 'تم تحديث بيانات الدليفري.');
}





    private function operationsPayload(Request $request, Workspace $workspace): array
{
    $status = $request->input('status', 'active');

    $ordersQuery = $workspace->restaurantOrders()
        ->with([
            'branch:id,name',
            'items',
    'deliveryZone:id,name',
    'deliveryCourier:id,name,phone',
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

            'accepted' => $workspace->restaurantOrders()
    ->where('status', 'accepted')
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


public function receipt(Request $request, Workspace $workspace, RestaurantOrder $restaurantOrder)
{
    $this->ensureOrderBelongsToWorkspace($workspace, $restaurantOrder);

    $type = $request->query('type', 'client');

    abort_if(! in_array($type, ['client', 'kitchen', 'both'], true), 404);

    $copies = match ($type) {
        'kitchen' => ['kitchen'],
        'both' => ['kitchen', 'client'],
        default => ['client'],
    };

    $restaurantOrder->load([
        'branch',
        'invoice',
        'items.options',
    'deliveryZone',
    'deliveryCourier',
    ]);

    $profile = $workspace->businessProfile;

    return view('app.restaurant-menu.orders.receipt', compact(
        'workspace',
        'restaurantOrder',
        'profile',
        'type',
        'copies'
    ));
}
public function receiptX(Workspace $workspace, RestaurantOrder $restaurantOrder)
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