<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\RestaurantMenu\StoreRestaurantItemOptionRequest;
use App\Http\Requests\App\RestaurantMenu\UpdateRestaurantItemOptionRequest;
use App\Models\RestaurantMenu\RestaurantItemOption;
use App\Models\RestaurantMenu\RestaurantItemOptionGroup;
use App\Models\RestaurantMenu\RestaurantMenuItem;
use App\Models\Workspace;
use App\Services\App\RestaurantMenu\RestaurantItemOptionService;
use App\Services\Core\FeatureLimitService;
use Illuminate\Http\Request;

class RestaurantItemOptionController extends Controller
{
    public function __construct(
        private readonly RestaurantItemOptionService $restaurantItemOptionService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function index(
        Request $request,
        Workspace $workspace,
        RestaurantMenuItem $restaurantMenuItem,
        RestaurantItemOptionGroup $restaurantItemOptionGroup
    ) {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);
        $this->ensureGroupBelongsToItem($restaurantMenuItem, $restaurantItemOptionGroup);

        if (! $this->featureLimitService->enabled($workspace, 'restaurant_item_addons_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.items.index', $workspace)
                ->with('error', 'ميزة الإضافات غير متاحة في باقتك الحالية.');
        }

        $restaurantMenuItem->loadMissing(['branch:id,name', 'category:id,name']);

        $options = $restaurantItemOptionGroup->options()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where('name', 'like', "%{$search}%");
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

        $optionsLimit = $this->featureLimitService->limit(
            workspace: $workspace,
            featureKey: 'restaurant_options_limit',
            default: 0
        );

        $isUnlimited = $optionsLimit === -1;

        return view('app.restaurant-menu.options.index', compact(
            'workspace',
            'restaurantMenuItem',
            'restaurantItemOptionGroup',
            'options',
            'optionsLimit',
            'isUnlimited'
        ));
    }

    public function create(
        Workspace $workspace,
        RestaurantMenuItem $restaurantMenuItem,
        RestaurantItemOptionGroup $restaurantItemOptionGroup
    ) {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);
        $this->ensureGroupBelongsToItem($restaurantMenuItem, $restaurantItemOptionGroup);

        if (! $this->featureLimitService->enabled($workspace, 'restaurant_item_addons_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.items.index', $workspace)
                ->with('error', 'ميزة الإضافات غير متاحة في باقتك الحالية.');
        }

        $currentCount = $workspace->restaurantItemOptions()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_options_limit', $currentCount)) {
            return redirect()
                ->route('app.restaurant-menu.items.option-groups.options.index', [
                    $workspace,
                    $restaurantMenuItem,
                    $restaurantItemOptionGroup,
                ])
                ->with('error', 'وصلت للحد الأقصى للخيارات في باقتك الحالية.');
        }

        return view('app.restaurant-menu.options.create', compact(
            'workspace',
            'restaurantMenuItem',
            'restaurantItemOptionGroup'
        ));
    }

    public function store(
        StoreRestaurantItemOptionRequest $request,
        Workspace $workspace,
        RestaurantMenuItem $restaurantMenuItem,
        RestaurantItemOptionGroup $restaurantItemOptionGroup
    ) {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);
        $this->ensureGroupBelongsToItem($restaurantMenuItem, $restaurantItemOptionGroup);

        if (! $this->featureLimitService->enabled($workspace, 'restaurant_item_addons_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.items.index', $workspace)
                ->with('error', 'ميزة الإضافات غير متاحة في باقتك الحالية.');
        }

        $currentCount = $workspace->restaurantItemOptions()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_options_limit', $currentCount)) {
            return redirect()
                ->route('app.restaurant-menu.items.option-groups.options.index', [
                    $workspace,
                    $restaurantMenuItem,
                    $restaurantItemOptionGroup,
                ])
                ->with('error', 'وصلت للحد الأقصى للخيارات في باقتك الحالية.');
        }

        $this->restaurantItemOptionService->create(
            workspace: $workspace,
            item: $restaurantMenuItem,
            group: $restaurantItemOptionGroup,
            data: $request->validated()
        );

        return redirect()
            ->route('app.restaurant-menu.items.option-groups.options.index', [
                $workspace,
                $restaurantMenuItem,
                $restaurantItemOptionGroup,
            ])
            ->with('success', 'تم إضافة الخيار بنجاح.');
    }

    public function edit(
        Workspace $workspace,
        RestaurantMenuItem $restaurantMenuItem,
        RestaurantItemOptionGroup $restaurantItemOptionGroup,
        RestaurantItemOption $restaurantItemOption
    ) {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);
        $this->ensureGroupBelongsToItem($restaurantMenuItem, $restaurantItemOptionGroup);
        $this->ensureOptionBelongsToGroup($restaurantItemOptionGroup, $restaurantItemOption);

        return view('app.restaurant-menu.options.edit', compact(
            'workspace',
            'restaurantMenuItem',
            'restaurantItemOptionGroup',
            'restaurantItemOption'
        ));
    }

    public function update(
        UpdateRestaurantItemOptionRequest $request,
        Workspace $workspace,
        RestaurantMenuItem $restaurantMenuItem,
        RestaurantItemOptionGroup $restaurantItemOptionGroup,
        RestaurantItemOption $restaurantItemOption
    ) {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);
        $this->ensureGroupBelongsToItem($restaurantMenuItem, $restaurantItemOptionGroup);
        $this->ensureOptionBelongsToGroup($restaurantItemOptionGroup, $restaurantItemOption);

        $this->restaurantItemOptionService->update(
            option: $restaurantItemOption,
            data: $request->validated()
        );

        return redirect()
            ->route('app.restaurant-menu.items.option-groups.options.index', [
                $workspace,
                $restaurantMenuItem,
                $restaurantItemOptionGroup,
            ])
            ->with('success', 'تم تحديث الخيار بنجاح.');
    }

    public function destroy(
        Workspace $workspace,
        RestaurantMenuItem $restaurantMenuItem,
        RestaurantItemOptionGroup $restaurantItemOptionGroup,
        RestaurantItemOption $restaurantItemOption
    ) {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);
        $this->ensureGroupBelongsToItem($restaurantMenuItem, $restaurantItemOptionGroup);
        $this->ensureOptionBelongsToGroup($restaurantItemOptionGroup, $restaurantItemOption);

        $this->restaurantItemOptionService->delete($restaurantItemOption);

        return redirect()
            ->route('app.restaurant-menu.items.option-groups.options.index', [
                $workspace,
                $restaurantMenuItem,
                $restaurantItemOptionGroup,
            ])
            ->with('success', 'تم حذف الخيار بنجاح.');
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

    private function ensureOptionBelongsToGroup(
        RestaurantItemOptionGroup $restaurantItemOptionGroup,
        RestaurantItemOption $restaurantItemOption
    ): void {
        abort_if((int) $restaurantItemOption->option_group_id !== (int) $restaurantItemOptionGroup->id, 404);
    }
}