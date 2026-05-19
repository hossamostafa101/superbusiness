<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\RestaurantMenu\StoreRestaurantItemVariantRequest;
use App\Http\Requests\App\RestaurantMenu\UpdateRestaurantItemVariantRequest;
use App\Models\RestaurantMenu\RestaurantItemVariant;
use App\Models\RestaurantMenu\RestaurantMenuItem;
use App\Models\Workspace;
use App\Services\App\RestaurantMenu\RestaurantItemVariantService;
use App\Services\Core\FeatureLimitService;
use Illuminate\Http\Request;

class RestaurantItemVariantController extends Controller
{
    public function __construct(
        private readonly RestaurantItemVariantService $restaurantItemVariantService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function index(Request $request, Workspace $workspace, RestaurantMenuItem $restaurantMenuItem)
    {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);

        $restaurantMenuItem->loadMissing(['branch:id,name', 'category:id,name']);

        if (! $this->featureLimitService->enabled($workspace, 'restaurant_item_variants_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.items.index', $workspace)
                ->with('error', 'ميزة Variants غير متاحة في باقتك الحالية.');
        }

        $variants = $restaurantMenuItem->variants()
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
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        $variantsLimit = $this->featureLimitService->limit(
            workspace: $workspace,
            featureKey: 'restaurant_item_variants_limit',
            default: 0
        );

        $isUnlimited = $variantsLimit === -1;

        return view('app.restaurant-menu.variants.index', compact(
            'workspace',
            'restaurantMenuItem',
            'variants',
            'variantsLimit',
            'isUnlimited'
        ));
    }

    public function create(Workspace $workspace, RestaurantMenuItem $restaurantMenuItem)
    {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);

        if (! $this->featureLimitService->enabled($workspace, 'restaurant_item_variants_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.items.index', $workspace)
                ->with('error', 'ميزة Variants غير متاحة في باقتك الحالية.');
        }

        $currentCount = $workspace->restaurantItemVariants()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_item_variants_limit', $currentCount)) {
            return redirect()
                ->route('app.restaurant-menu.items.variants.index', [$workspace, $restaurantMenuItem])
                ->with('error', 'وصلت للحد الأقصى للـ Variants في باقتك الحالية.');
        }

        return view('app.restaurant-menu.variants.create', compact(
            'workspace',
            'restaurantMenuItem'
        ));
    }

    public function store(StoreRestaurantItemVariantRequest $request, Workspace $workspace, RestaurantMenuItem $restaurantMenuItem)
    {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);

        if (! $this->featureLimitService->enabled($workspace, 'restaurant_item_variants_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.items.index', $workspace)
                ->with('error', 'ميزة Variants غير متاحة في باقتك الحالية.');
        }

        $currentCount = $workspace->restaurantItemVariants()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_item_variants_limit', $currentCount)) {
            return redirect()
                ->route('app.restaurant-menu.items.variants.index', [$workspace, $restaurantMenuItem])
                ->with('error', 'وصلت للحد الأقصى للـ Variants في باقتك الحالية.');
        }

        $this->restaurantItemVariantService->create(
            workspace: $workspace,
            item: $restaurantMenuItem,
            data: $request->validated()
        );

        return redirect()
            ->route('app.restaurant-menu.items.variants.index', [$workspace, $restaurantMenuItem])
            ->with('success', 'تم إضافة Variant بنجاح.');
    }

    public function edit(Workspace $workspace, RestaurantMenuItem $restaurantMenuItem, RestaurantItemVariant $restaurantItemVariant)
    {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);
        $this->ensureVariantBelongsToItem($restaurantMenuItem, $restaurantItemVariant);

        return view('app.restaurant-menu.variants.edit', compact(
            'workspace',
            'restaurantMenuItem',
            'restaurantItemVariant'
        ));
    }

    public function update(UpdateRestaurantItemVariantRequest $request, Workspace $workspace, RestaurantMenuItem $restaurantMenuItem, RestaurantItemVariant $restaurantItemVariant)
    {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);
        $this->ensureVariantBelongsToItem($restaurantMenuItem, $restaurantItemVariant);

        $this->restaurantItemVariantService->update(
            item: $restaurantMenuItem,
            variant: $restaurantItemVariant,
            data: $request->validated()
        );

        return redirect()
            ->route('app.restaurant-menu.items.variants.index', [$workspace, $restaurantMenuItem])
            ->with('success', 'تم تحديث Variant بنجاح.');
    }

    public function destroy(Workspace $workspace, RestaurantMenuItem $restaurantMenuItem, RestaurantItemVariant $restaurantItemVariant)
    {
        $this->ensureItemBelongsToWorkspace($workspace, $restaurantMenuItem);
        $this->ensureVariantBelongsToItem($restaurantMenuItem, $restaurantItemVariant);

        $this->restaurantItemVariantService->delete(
            item: $restaurantMenuItem,
            variant: $restaurantItemVariant
        );

        return redirect()
            ->route('app.restaurant-menu.items.variants.index', [$workspace, $restaurantMenuItem])
            ->with('success', 'تم حذف Variant بنجاح.');
    }

    private function ensureItemBelongsToWorkspace(Workspace $workspace, RestaurantMenuItem $restaurantMenuItem): void
    {
        abort_if((int) $restaurantMenuItem->workspace_id !== (int) $workspace->id, 404);
    }

    private function ensureVariantBelongsToItem(RestaurantMenuItem $restaurantMenuItem, RestaurantItemVariant $restaurantItemVariant): void
    {
        abort_if((int) $restaurantItemVariant->item_id !== (int) $restaurantMenuItem->id, 404);
    }
}