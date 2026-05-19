<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\StoreBusinessProductRequest;
use App\Http\Requests\App\UpdateBusinessProductRequest;
use App\Models\BusinessProduct;
use App\Models\Workspace;
use App\Services\App\BusinessProductService;
use App\Services\Core\FeatureLimitService;
use Illuminate\Http\Request;

class BusinessProductController extends Controller
{
    public function __construct(
        private readonly BusinessProductService $businessProductService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function index(Request $request, Workspace $workspace)
    {
        $products = $workspace->businessProducts()
            ->with('category:id,name')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->input('category_id'));
            })
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $categories = $workspace->businessCategories()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        $productsLimit = $this->featureLimitService->limit($workspace, 'products_limit', 5);
        $isUnlimited = $productsLimit === -1;

        return view('app.business-products.index', compact(
            'workspace',
            'products',
            'categories',
            'productsLimit',
            'isUnlimited'
        ));
    }

    public function create(Workspace $workspace)
    {
        $currentCount = $workspace->businessProducts()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'products_limit', $currentCount)) {
            return redirect()
                ->route('app.products.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى للمنتجات في باقتك الحالية.');
        }

        $categories = $workspace->businessCategories()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('app.business-products.create', compact('workspace', 'categories'));
    }

    public function store(StoreBusinessProductRequest $request, Workspace $workspace)
    {
        $currentCount = $workspace->businessProducts()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'products_limit', $currentCount)) {
            return redirect()
                ->route('app.products.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى للمنتجات في باقتك الحالية.');
        }

        $this->businessProductService->create(
            workspace: $workspace,
            data: $request->validated(),
            image: $request->file('image')
        );

        return redirect()
            ->route('app.products.index', $workspace)
            ->with('success', 'تم إضافة المنتج بنجاح.');
    }

    public function edit(Workspace $workspace, BusinessProduct $businessProduct)
    {
        $this->ensureProductBelongsToWorkspace($workspace, $businessProduct);

        $categories = $workspace->businessCategories()
            ->where(function ($query) use ($businessProduct) {
                $query->where('is_active', true);

                if ($businessProduct->category_id) {
                    $query->orWhere('id', $businessProduct->category_id);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('app.business-products.edit', compact('workspace', 'businessProduct', 'categories'));
    }

    public function update(UpdateBusinessProductRequest $request, Workspace $workspace, BusinessProduct $businessProduct)
    {
        $this->ensureProductBelongsToWorkspace($workspace, $businessProduct);

        $this->businessProductService->update(
            product: $businessProduct,
            data: $request->validated(),
            image: $request->file('image')
        );

        return redirect()
            ->route('app.products.index', $workspace)
            ->with('success', 'تم تحديث المنتج بنجاح.');
    }

    public function destroy(Workspace $workspace, BusinessProduct $businessProduct)
    {
        $this->ensureProductBelongsToWorkspace($workspace, $businessProduct);

        $this->businessProductService->delete($businessProduct);

        return redirect()
            ->route('app.products.index', $workspace)
            ->with('success', 'تم حذف المنتج بنجاح.');
    }

    private function ensureProductBelongsToWorkspace(Workspace $workspace, BusinessProduct $businessProduct): void
    {
        abort_if((int) $businessProduct->workspace_id !== (int) $workspace->id, 404);
    }
}