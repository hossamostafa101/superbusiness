<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFeatureRequest;
use App\Http\Requests\Admin\UpdateFeatureRequest;
use App\Models\Feature;
use App\Services\Admin\FeatureService;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function __construct(
        private readonly FeatureService $featureService
    ) {
        $this->middleware('admin.permission:features.view')->only(['index']);
$this->middleware('admin.permission:features.create')->only(['create', 'store']);
$this->middleware('admin.permission:features.edit')->only(['edit', 'update', 'toggleStatus']);
$this->middleware('admin.permission:features.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $features = Feature::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->string('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('key', 'like', "%{$search}%")
                        ->orWhere('module', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->string('type'));
            })
            ->when($request->filled('module'), function ($query) use ($request) {
                $query->where('module', $request->string('module'));
            })
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $modules = Feature::query()
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        return view('admin.sections.features.index', compact('features', 'modules'));
    }

    public function create()
    {
        return view('admin.sections.features.create');
    }

    public function store(StoreFeatureRequest $request)
    {
        $this->featureService->create($request->validated());

        return redirect()
            ->route('admin.features.index')
            ->with('success', 'تم إنشاء الخاصية بنجاح.');
    }

    public function edit(Feature $feature)
    {
        return view('admin.sections.features.edit', compact('feature'));
    }

    public function update(UpdateFeatureRequest $request, Feature $feature)
    {
        $this->featureService->update($feature, $request->validated());

        return redirect()
            ->route('admin.features.index')
            ->with('success', 'تم تحديث الخاصية بنجاح.');
    }

    public function destroy(Feature $feature)
    {
        if ($feature->plans()->exists()) {
            return back()->with('error', 'لا يمكن حذف خاصية مرتبطة بباقات. قم بإزالتها من الباقات أولًا.');
        }

        $this->featureService->delete($feature);

        return redirect()
            ->route('admin.features.index')
            ->with('success', 'تم حذف الخاصية بنجاح.');
    }

    public function toggleStatus(Feature $feature)
    {
        $this->featureService->toggleStatus($feature);

        return back()->with('success', 'تم تغيير حالة الخاصية بنجاح.');
    }
}