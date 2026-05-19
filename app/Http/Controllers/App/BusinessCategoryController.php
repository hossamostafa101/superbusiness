<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\StoreBusinessCategoryRequest;
use App\Http\Requests\App\UpdateBusinessCategoryRequest;
use App\Models\BusinessCategory;
use App\Models\Workspace;
use App\Services\App\BusinessCategoryService;

class BusinessCategoryController extends Controller
{
    public function __construct(
        private readonly BusinessCategoryService $businessCategoryService
    ) {}

    public function index(Workspace $workspace)
    {
        $categories = $workspace->businessCategories()
            ->withCount('products')
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(15);

        return view('app.business-categories.index', compact('workspace', 'categories'));
    }

    public function create(Workspace $workspace)
    {
        return view('app.business-categories.create', compact('workspace'));
    }

    public function store(StoreBusinessCategoryRequest $request, Workspace $workspace)
    {
        $this->businessCategoryService->create(
            workspace: $workspace,
            data: $request->validated()
        );

        return redirect()
            ->route('app.categories.index', $workspace)
            ->with('success', 'تم إضافة التصنيف بنجاح.');
    }

    public function edit(Workspace $workspace, BusinessCategory $businessCategory)
    {
        $this->ensureCategoryBelongsToWorkspace($workspace, $businessCategory);

        return view('app.business-categories.edit', compact('workspace', 'businessCategory'));
    }

    public function update(UpdateBusinessCategoryRequest $request, Workspace $workspace, BusinessCategory $businessCategory)
    {
        $this->ensureCategoryBelongsToWorkspace($workspace, $businessCategory);

        $this->businessCategoryService->update(
            category: $businessCategory,
            data: $request->validated()
        );

        return redirect()
            ->route('app.categories.index', $workspace)
            ->with('success', 'تم تحديث التصنيف بنجاح.');
    }

    public function destroy(Workspace $workspace, BusinessCategory $businessCategory)
    {
        $this->ensureCategoryBelongsToWorkspace($workspace, $businessCategory);

        $this->businessCategoryService->delete($businessCategory);

        return redirect()
            ->route('app.categories.index', $workspace)
            ->with('success', 'تم حذف التصنيف بنجاح.');
    }

    private function ensureCategoryBelongsToWorkspace(Workspace $workspace, BusinessCategory $businessCategory): void
    {
        abort_if((int) $businessCategory->workspace_id !== (int) $workspace->id, 404);
    }
}