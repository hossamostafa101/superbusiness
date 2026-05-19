<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\RestaurantMenu\StoreRestaurantItemOptionGroupRequest;
use App\Http\Requests\App\RestaurantMenu\UpdateRestaurantItemOptionGroupRequest;
use App\Models\RestaurantMenu\RestaurantItemOptionGroup;
use App\Models\RestaurantMenu\RestaurantMenuItem;
use App\Models\Workspace;
use App\Services\App\RestaurantMenu\RestaurantItemOptionGroupService;
use App\Services\Core\FeatureLimitService;
use Illuminate\Http\Request;

class RestaurantItemOptionGroupController extends Controller
{
    public function __construct(
        private readonly RestaurantItemOptionGroupService $restaurantItemOptionGroupService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function index(Request $request, Workspace $workspace, RestaurantMenuItem $restaurantMenuItem)
    {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);

        if (! $this->featureLimitService->enabled($workspace, 'restaurant_item_addons_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.items.index', $workspace)
                ->with('error', 'ميزة الإضافات غير متاحة في باقتك الحالية.');
        }

        $restaurantMenuItem->loadMissing(['branch:id,name', 'category:id,name']);

        $groups = $restaurantMenuItem->optionGroups()
            ->withCount('options')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
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
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        $groupsLimit = $this->featureLimitService->limit(
            workspace: $workspace,
            featureKey: 'restaurant_option_groups_limit',
            default: 0
        );

        $isUnlimited = $groupsLimit === -1;

        return view('app.restaurant-menu.option-groups.index', compact(
            'workspace',
            'restaurantMenuItem',
            'groups',
            'groupsLimit',
            'isUnlimited'
        ));
    }

    public function create(Workspace $workspace, RestaurantMenuItem $restaurantMenuItem)
    {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);

        if (! $this->featureLimitService->enabled($workspace, 'restaurant_item_addons_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.items.index', $workspace)
                ->with('error', 'ميزة الإضافات غير متاحة في باقتك الحالية.');
        }

        $currentCount = $workspace->restaurantItemOptionGroups()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_option_groups_limit', $currentCount)) {
            return redirect()
                ->route('app.restaurant-menu.items.option-groups.index', [$workspace, $restaurantMenuItem])
                ->with('error', 'وصلت للحد الأقصى لمجموعات الإضافات في باقتك الحالية.');
        }

        return view('app.restaurant-menu.option-groups.create', compact(
            'workspace',
            'restaurantMenuItem'
        ));
    }

    public function store(StoreRestaurantItemOptionGroupRequest $request, Workspace $workspace, RestaurantMenuItem $restaurantMenuItem)
    {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);

        if (! $this->featureLimitService->enabled($workspace, 'restaurant_item_addons_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.items.index', $workspace)
                ->with('error', 'ميزة الإضافات غير متاحة في باقتك الحالية.');
        }

        $currentCount = $workspace->restaurantItemOptionGroups()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_option_groups_limit', $currentCount)) {
            return redirect()
                ->route('app.restaurant-menu.items.option-groups.index', [$workspace, $restaurantMenuItem])
                ->with('error', 'وصلت للحد الأقصى لمجموعات الإضافات في باقتك الحالية.');
        }

        $this->restaurantItemOptionGroupService->create(
            workspace: $workspace,
            item: $restaurantMenuItem,
            data: $request->validated()
        );

        return redirect()
            ->route('app.restaurant-menu.items.option-groups.index', [$workspace, $restaurantMenuItem])
            ->with('success', 'تم إضافة مجموعة الإضافات بنجاح.');
    }

    public function edit(
        Workspace $workspace,
        RestaurantMenuItem $restaurantMenuItem,
        RestaurantItemOptionGroup $restaurantItemOptionGroup
    ) {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);
        $this->ensureGroupBelongsToItem($restaurantMenuItem, $restaurantItemOptionGroup);

        return view('app.restaurant-menu.option-groups.edit', compact(
            'workspace',
            'restaurantMenuItem',
            'restaurantItemOptionGroup'
        ));
    }

    public function update(
        UpdateRestaurantItemOptionGroupRequest $request,
        Workspace $workspace,
        RestaurantMenuItem $restaurantMenuItem,
        RestaurantItemOptionGroup $restaurantItemOptionGroup
    ) {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);
        $this->ensureGroupBelongsToItem($restaurantMenuItem, $restaurantItemOptionGroup);

        $this->restaurantItemOptionGroupService->update(
            group: $restaurantItemOptionGroup,
            data: $request->validated()
        );

        return redirect()
            ->route('app.restaurant-menu.items.option-groups.index', [$workspace, $restaurantMenuItem])
            ->with('success', 'تم تحديث مجموعة الإضافات بنجاح.');
    }

    public function destroy(
        Workspace $workspace,
        RestaurantMenuItem $restaurantMenuItem,
        RestaurantItemOptionGroup $restaurantItemOptionGroup
    ) {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);
        $this->ensureGroupBelongsToItem($restaurantMenuItem, $restaurantItemOptionGroup);

        $this->restaurantItemOptionGroupService->delete($restaurantItemOptionGroup);

        return redirect()
            ->route('app.restaurant-menu.items.option-groups.index', [$workspace, $restaurantMenuItem])
            ->with('success', 'تم حذف مجموعة الإضافات بنجاح.');
    }

    private function ensureItemBelongsToWorkspace(Workspace $workspace, RestaurantMenuItem $restaurantMenuItem): void
    {
        abort_if((int) $restaurantMenuItem->workspace_id !== (int) $workspace->id, 404);
    }

    private function ensureGroupBelongsToItem(
        RestaurantMenuItem $restaurantMenuItem,
        RestaurantItemOptionGroup $restaurantItemOptionGroup
    ): void {
        abort_if((int) $restaurantItemOptionGroup->item_id !== (int) $restaurantMenuItem->id, 404);
    }
}