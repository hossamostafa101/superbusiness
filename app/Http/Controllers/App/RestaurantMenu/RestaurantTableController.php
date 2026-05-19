<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\RestaurantMenu\StoreRestaurantTableRequest;
use App\Http\Requests\App\RestaurantMenu\UpdateRestaurantTableRequest;
use App\Models\RestaurantMenu\RestaurantTable;
use App\Models\Workspace;
use App\Services\App\RestaurantMenu\RestaurantTableService;
use App\Services\Core\FeatureLimitService;
use Illuminate\Http\Request;

class RestaurantTableController extends Controller
{
    public function __construct(
        private readonly RestaurantTableService $restaurantTableService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function index(Request $request, Workspace $workspace)
    {
        if (! $this->featureLimitService->enabled($workspace, 'restaurant_tables_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.branches.index', $workspace)
                ->with('error', 'ميزة الطاولات غير متاحة في باقتك الحالية.');
        }

        $tables = $workspace->restaurantTables()
            ->with('branch:id,name,slug')
            ->withCount('orders')
            ->when($request->filled('branch_id'), function ($query) use ($request) {
                $query->where('branch_id', $request->input('branch_id'));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('number', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->input('status') === 'active') {
                    $query->where('is_active', true);
                }

                if ($request->input('status') === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $branches = $workspace->restaurantBranches()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        $tablesLimit = $this->featureLimitService->limit(
            workspace: $workspace,
            featureKey: 'restaurant_tables_limit',
            default: 0
        );

        $isUnlimited = $tablesLimit === -1;

        $qrEnabled = $this->featureLimitService->enabled(
            workspace: $workspace,
            featureKey: 'restaurant_table_qr_enabled'
        );

        return view('app.restaurant-menu.tables.index', compact(
            'workspace',
            'tables',
            'branches',
            'tablesLimit',
            'isUnlimited',
            'qrEnabled'
        ));
    }

    public function create(Workspace $workspace)
    {
        if (! $this->featureLimitService->enabled($workspace, 'restaurant_tables_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.branches.index', $workspace)
                ->with('error', 'ميزة الطاولات غير متاحة في باقتك الحالية.');
        }

        $currentCount = $workspace->restaurantTables()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_tables_limit', $currentCount)) {
            return redirect()
                ->route('app.restaurant-menu.tables.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى للطاولات في باقتك الحالية.');
        }

        $branches = $workspace->restaurantBranches()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($branches->isEmpty()) {
            return redirect()
                ->route('app.restaurant-menu.branches.create', $workspace)
                ->with('error', 'يجب إضافة فرع أولًا قبل إضافة الطاولات.');
        }

        return view('app.restaurant-menu.tables.create', compact(
            'workspace',
            'branches'
        ));
    }

    public function store(StoreRestaurantTableRequest $request, Workspace $workspace)
    {
        if (! $this->featureLimitService->enabled($workspace, 'restaurant_tables_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.branches.index', $workspace)
                ->with('error', 'ميزة الطاولات غير متاحة في باقتك الحالية.');
        }

        $currentCount = $workspace->restaurantTables()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_tables_limit', $currentCount)) {
            return redirect()
                ->route('app.restaurant-menu.tables.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى للطاولات في باقتك الحالية.');
        }

        try {
            $this->restaurantTableService->create(
                workspace: $workspace,
                data: $request->validated()
            );

            return redirect()
                ->route('app.restaurant-menu.tables.index', $workspace)
                ->with('success', 'تم إضافة الطاولة بنجاح.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit(Workspace $workspace, RestaurantTable $restaurantTable)
    {
        $this->ensureTableBelongsToWorkspace($workspace, $restaurantTable);

        $branches = $workspace->restaurantBranches()
            ->where(function ($query) use ($restaurantTable) {
                $query->where('is_active', true)
                    ->orWhere('id', $restaurantTable->branch_id);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('app.restaurant-menu.tables.edit', compact(
            'workspace',
            'restaurantTable',
            'branches'
        ));
    }

    public function update(UpdateRestaurantTableRequest $request, Workspace $workspace, RestaurantTable $restaurantTable)
    {
        $this->ensureTableBelongsToWorkspace($workspace, $restaurantTable);

        try {
            $this->restaurantTableService->update(
                workspace: $workspace,
                table: $restaurantTable,
                data: $request->validated()
            );

            return redirect()
                ->route('app.restaurant-menu.tables.index', $workspace)
                ->with('success', 'تم تحديث الطاولة بنجاح.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function regenerateCode(Workspace $workspace, RestaurantTable $restaurantTable)
    {
        $this->ensureTableBelongsToWorkspace($workspace, $restaurantTable);

        $this->restaurantTableService->regenerateCode(
            workspace: $workspace,
            table: $restaurantTable
        );

        return back()->with('success', 'تم إنشاء رابط QR جديد للطاولة.');
    }

    public function destroy(Workspace $workspace, RestaurantTable $restaurantTable)
    {
        $this->ensureTableBelongsToWorkspace($workspace, $restaurantTable);

        $this->restaurantTableService->delete($restaurantTable);

        return redirect()
            ->route('app.restaurant-menu.tables.index', $workspace)
            ->with('success', 'تم حذف الطاولة بنجاح.');
    }

    private function ensureTableBelongsToWorkspace(Workspace $workspace, RestaurantTable $restaurantTable): void
    {
        abort_if((int) $restaurantTable->workspace_id !== (int) $workspace->id, 404);
    }





    public function printOne(Workspace $workspace, RestaurantTable $restaurantTable)
{
    $this->ensureTableBelongsToWorkspace($workspace, $restaurantTable);

    $restaurantTable->load(['branch:id,workspace_id,name,slug', 'workspace']);

    $publicUrl = route('public.restaurant-menu.branch', [
    'workspace' => $workspace,
    'branch' => $restaurantTable->branch,
]) . '?table_code=' . urlencode($restaurantTable->code);

    $qrImage = 'https://api.qrserver.com/v1/create-qr-code/?size=420x420&data=' . urlencode($publicUrl);

    return view('app.restaurant-menu.tables.print-one', compact(
        'workspace',
        'restaurantTable',
        'publicUrl',
        'qrImage'
    ));
}

public function printAll(Request $request, Workspace $workspace)
{
    $query = $workspace->restaurantTables()
        ->with(['branch:id,workspace_id,name,slug', 'workspace'])
        ->where('is_active', true);

    if ($request->filled('branch_id')) {
        $query->where('branch_id', $request->input('branch_id'));
    }

    $tables = $query
        ->orderBy('branch_id')
        ->orderBy('sort_order')
        ->orderBy('number')
        ->get();

    return view('app.restaurant-menu.tables.print-all', compact(
        'workspace',
        'tables'
    ));
}
}