<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\RestaurantMenu\StoreRestaurantBranchRequest;
use App\Http\Requests\App\RestaurantMenu\UpdateRestaurantBranchRequest;
use App\Models\RestaurantMenu\RestaurantBranch;
use App\Models\Workspace;
use App\Services\App\RestaurantMenu\RestaurantBranchService;
use App\Services\Core\FeatureLimitService;
use Illuminate\Http\Request;

class RestaurantBranchController extends Controller
{
    public function __construct(
        private readonly RestaurantBranchService $restaurantBranchService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function index(Request $request, Workspace $workspace)
    {
        $branches = $workspace->restaurantBranches()
            ->withCount(['categories', 'items'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('whatsapp_number', 'like', "%{$search}%");
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

        $branchesLimit = $this->featureLimitService->limit(
            workspace: $workspace,
            featureKey: 'restaurant_branches_limit',
            default: 1
        );

        $isUnlimited = $branchesLimit === -1;

        return view('app.restaurant-menu.branches.index', compact(
            'workspace',
            'branches',
            'branchesLimit',
            'isUnlimited'
        ));
    }

    public function create(Workspace $workspace)
    {
        $currentCount = $workspace->restaurantBranches()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_branches_limit', $currentCount)) {
            return redirect()
                ->route('app.restaurant-menu.branches.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى للفروع في باقتك الحالية.');
        }

        $cloneEnabled = $this->featureLimitService->enabled(
            workspace: $workspace,
            featureKey: 'restaurant_branch_clone_enabled'
        );

        $cloneBranches = collect();

        if ($cloneEnabled) {
            $cloneBranches = $workspace->restaurantBranches()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return view('app.restaurant-menu.branches.create', compact(
            'workspace',
            'cloneEnabled',
            'cloneBranches'
        ));
    }

    public function store(StoreRestaurantBranchRequest $request, Workspace $workspace)
    {
        $currentCount = $workspace->restaurantBranches()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'restaurant_branches_limit', $currentCount)) {
            return redirect()
                ->route('app.restaurant-menu.branches.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى للفروع في باقتك الحالية.');
        }

        $data = $request->validated();

        $cloneEnabled = $this->featureLimitService->enabled(
            workspace: $workspace,
            featureKey: 'restaurant_branch_clone_enabled'
        );

        if (! $cloneEnabled) {
            unset($data['clone_from_branch_id']);
        }

        $this->restaurantBranchService->create(
            workspace: $workspace,
            data: $data
        );

        return redirect()
            ->route('app.restaurant-menu.branches.index', $workspace)
            ->with('success', 'تم إنشاء الفرع بنجاح.');
    }

    public function edit(Workspace $workspace, RestaurantBranch $restaurantBranch)
    {
        $this->ensureBranchBelongsToWorkspace($workspace, $restaurantBranch);

        return view('app.restaurant-menu.branches.edit', compact(
            'workspace',
            'restaurantBranch'
        ));
    }

    public function update(UpdateRestaurantBranchRequest $request, Workspace $workspace, RestaurantBranch $restaurantBranch)
    {
        $this->ensureBranchBelongsToWorkspace($workspace, $restaurantBranch);

        $this->restaurantBranchService->update(
            workspace: $workspace,
            branch: $restaurantBranch,
            data: $request->validated()
        );

        return redirect()
            ->route('app.restaurant-menu.branches.index', $workspace)
            ->with('success', 'تم تحديث بيانات الفرع بنجاح.');
    }

    public function destroy(Workspace $workspace, RestaurantBranch $restaurantBranch)
    {
        $this->ensureBranchBelongsToWorkspace($workspace, $restaurantBranch);

        $this->restaurantBranchService->delete(
            workspace: $workspace,
            branch: $restaurantBranch
        );

        return redirect()
            ->route('app.restaurant-menu.branches.index', $workspace)
            ->with('success', 'تم حذف الفرع بنجاح.');
    }

    private function ensureBranchBelongsToWorkspace(Workspace $workspace, RestaurantBranch $restaurantBranch): void
    {
        abort_if((int) $restaurantBranch->workspace_id !== (int) $workspace->id, 404);
    }
}