<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\RestaurantMenu\StoreRestaurantMenuCategoryRequest;
use App\Http\Requests\App\RestaurantMenu\UpdateRestaurantMenuCategoryRequest;
use App\Models\RestaurantMenu\RestaurantMenuCategory;
use App\Models\Workspace;
use App\Services\App\RestaurantMenu\RestaurantMenuCategoryService;
use App\Services\Core\FeatureLimitService;
use Illuminate\Http\Request;

class RestaurantMenuCategoryController extends Controller
{
    public function __construct(
        private readonly RestaurantMenuCategoryService $restaurantMenuCategoryService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function index(Request $request, Workspace $workspace)
    {
        $categories = $workspace->restaurantMenuCategories()
            ->with('branch:id,name')
            ->withCount('items')
            ->when($request->filled('branch_id'), function ($query) use ($request) {
                $query->where('branch_id', $request->input('branch_id'));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
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

        $categoriesLimit = $this->featureLimitService->limit(
            workspace: $workspace,
            featureKey: 'restaurant_menu_categories_limit',
            default: 5
        );

        $isUnlimited = $categoriesLimit === -1;

        return view('app.restaurant-menu.categories.index', compact(
            'workspace',
            'categories',
            'branches',
            'categoriesLimit',
            'isUnlimited'
        ));
    }

    public function create(Workspace $workspace)
    {
        $currentCount = $workspace->restaurantMenuCategories()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_menu_categories_limit', $currentCount)) {
            return redirect()
                ->route('app.restaurant-menu.categories.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى لتصنيفات المنيو في باقتك الحالية.');
        }

        $branches = $workspace->restaurantBranches()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($branches->isEmpty()) {
            return redirect()
                ->route('app.restaurant-menu.branches.create', $workspace)
                ->with('error', 'يجب إضافة فرع أولًا قبل إنشاء تصنيفات المنيو.');
        }

        return view('app.restaurant-menu.categories.create', compact(
            'workspace',
            'branches'
        ));
    }

    public function store(StoreRestaurantMenuCategoryRequest $request, Workspace $workspace)
    {
        $currentCount = $workspace->restaurantMenuCategories()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_menu_categories_limit', $currentCount)) {
            return redirect()
                ->route('app.restaurant-menu.categories.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى لتصنيفات المنيو في باقتك الحالية.');
        }

        $this->restaurantMenuCategoryService->create(
            workspace: $workspace,
            data: $request->validated(),
            image: $request->file('image')
        );

        return redirect()
            ->route('app.restaurant-menu.categories.index', $workspace)
            ->with('success', 'تم إضافة تصنيف المنيو بنجاح.');
    }

    public function edit(Workspace $workspace, RestaurantMenuCategory $restaurantMenuCategory)
    {
        $this->ensureCategoryBelongsToWorkspace($workspace, $restaurantMenuCategory);

        $branches = $workspace->restaurantBranches()
            ->where(function ($query) use ($restaurantMenuCategory) {
                $query->where('is_active', true);

                if ($restaurantMenuCategory->branch_id) {
                    $query->orWhere('id', $restaurantMenuCategory->branch_id);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('app.restaurant-menu.categories.edit', compact(
            'workspace',
            'restaurantMenuCategory',
            'branches'
        ));
    }

    public function update(UpdateRestaurantMenuCategoryRequest $request, Workspace $workspace, RestaurantMenuCategory $restaurantMenuCategory)
    {
        $this->ensureCategoryBelongsToWorkspace($workspace, $restaurantMenuCategory);

        $this->restaurantMenuCategoryService->update(
            workspace: $workspace,
            category: $restaurantMenuCategory,
            data: $request->validated(),
            image: $request->file('image')
        );

        return redirect()
            ->route('app.restaurant-menu.categories.index', $workspace)
            ->with('success', 'تم تحديث تصنيف المنيو بنجاح.');
    }

    public function destroy(Workspace $workspace, RestaurantMenuCategory $restaurantMenuCategory)
    {
        $this->ensureCategoryBelongsToWorkspace($workspace, $restaurantMenuCategory);

        $this->restaurantMenuCategoryService->delete($restaurantMenuCategory);

        return redirect()
            ->route('app.restaurant-menu.categories.index', $workspace)
            ->with('success', 'تم حذف تصنيف المنيو بنجاح.');
    }

    private function ensureCategoryBelongsToWorkspace(Workspace $workspace, RestaurantMenuCategory $restaurantMenuCategory): void
    {
        abort_if((int) $restaurantMenuCategory->workspace_id !== (int) $workspace->id, 404);
    }
}