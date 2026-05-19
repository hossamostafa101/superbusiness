<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\RestaurantMenu\StoreRestaurantMenuItemRequest;
use App\Http\Requests\App\RestaurantMenu\UpdateRestaurantMenuItemRequest;
use App\Models\RestaurantMenu\RestaurantMenuItem;
use App\Models\Workspace;
use App\Services\App\RestaurantMenu\RestaurantMenuItemService;
use App\Services\Core\FeatureLimitService;
use Illuminate\Http\Request;

class RestaurantMenuItemController extends Controller
{
    public function __construct(
        private readonly RestaurantMenuItemService $restaurantMenuItemService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function index(Request $request, Workspace $workspace)
    {
        $items = $workspace->restaurantMenuItems()
            ->with([
                'branch:id,name',
                'category:id,name',
            ])
            ->when($request->filled('branch_id'), function ($query) use ($request) {
                $query->where('branch_id', $request->input('branch_id'));
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->input('category_id'));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('availability'), function ($query) use ($request) {
                if ($request->input('availability') === 'available') {
                    $query->where('is_available', true);
                }

                if ($request->input('availability') === 'unavailable') {
                    $query->where('is_available', false);
                }
            })
            ->when($request->filled('featured'), function ($query) use ($request) {
                if ($request->input('featured') === '1') {
                    $query->where('is_featured', true);
                }

                if ($request->input('featured') === '0') {
                    $query->where('is_featured', false);
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

        $categories = $workspace->restaurantMenuCategories()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'branch_id', 'name']);

        $itemsLimit = $this->featureLimitService->limit(
            workspace: $workspace,
            featureKey: 'restaurant_menu_items_limit',
            default: 30
        );

        $isUnlimited = $itemsLimit === -1;

        return view('app.restaurant-menu.items.index', compact(
            'workspace',
            'items',
            'branches',
            'categories',
            'itemsLimit',
            'isUnlimited'
        ));
    }

    public function create(Workspace $workspace)
    {
        $currentCount = $workspace->restaurantMenuItems()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_menu_items_limit', $currentCount)) {
            return redirect()
                ->route('app.restaurant-menu.items.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى لأصناف المنيو في باقتك الحالية.');
        }

        $branches = $workspace->restaurantBranches()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($branches->isEmpty()) {
            return redirect()
                ->route('app.restaurant-menu.branches.create', $workspace)
                ->with('error', 'يجب إضافة فرع أولًا قبل إنشاء أصناف المنيو.');
        }

        $categories = $workspace->restaurantMenuCategories()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'branch_id', 'name']);

        return view('app.restaurant-menu.items.create', compact(
            'workspace',
            'branches',
            'categories'
        ));
    }

    public function store(StoreRestaurantMenuItemRequest $request, Workspace $workspace)
    {
        $currentCount = $workspace->restaurantMenuItems()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_menu_items_limit', $currentCount)) {
            return redirect()
                ->route('app.restaurant-menu.items.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى لأصناف المنيو في باقتك الحالية.');
        }

        $this->restaurantMenuItemService->create(
            workspace: $workspace,
            data: $request->validated(),
            image: $request->file('image')
        );

        return redirect()
            ->route('app.restaurant-menu.items.index', $workspace)
            ->with('success', 'تم إضافة صنف المنيو بنجاح.');
    }

    public function edit(Workspace $workspace, RestaurantMenuItem $restaurantMenuItem)
    {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);

        $branches = $workspace->restaurantBranches()
            ->where(function ($query) use ($restaurantMenuItem) {
                $query->where('is_active', true);

                if ($restaurantMenuItem->branch_id) {
                    $query->orWhere('id', $restaurantMenuItem->branch_id);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        $categories = $workspace->restaurantMenuCategories()
            ->where(function ($query) use ($restaurantMenuItem) {
                $query->where('is_active', true);

                if ($restaurantMenuItem->category_id) {
                    $query->orWhere('id', $restaurantMenuItem->category_id);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'branch_id', 'name']);

        return view('app.restaurant-menu.items.edit', compact(
            'workspace',
            'restaurantMenuItem',
            'branches',
            'categories'
        ));
    }

    public function update(UpdateRestaurantMenuItemRequest $request, Workspace $workspace, RestaurantMenuItem $restaurantMenuItem)
    {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);

        $this->restaurantMenuItemService->update(
            workspace: $workspace,
            item: $restaurantMenuItem,
            data: $request->validated(),
            image: $request->file('image')
        );

        return redirect()
            ->route('app.restaurant-menu.items.index', $workspace)
            ->with('success', 'تم تحديث صنف المنيو بنجاح.');
    }

    public function destroy(Workspace $workspace, RestaurantMenuItem $restaurantMenuItem)
    {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);

        $this->restaurantMenuItemService->delete($restaurantMenuItem);

        return redirect()
            ->route('app.restaurant-menu.items.index', $workspace)
            ->with('success', 'تم حذف صنف المنيو بنجاح.');
    }

    private function ensureItemBelongsToWorkspace(Workspace $workspace, RestaurantMenuItem $restaurantMenuItem): void
    {
        abort_if((int) $restaurantMenuItem->workspace_id !== (int) $workspace->id, 404);
    }
}