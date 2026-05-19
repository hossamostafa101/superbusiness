<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePlanRequest;
use App\Http\Requests\Admin\UpdatePlanRequest;
use App\Models\Feature;
use App\Models\Plan;
use App\Services\Admin\PlanService;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function __construct(
        private readonly PlanService $planService
    ) {
       $this->middleware('admin.permission:plans.view')->only(['index']);
$this->middleware('admin.permission:plans.create')->only(['create', 'store']);
$this->middleware('admin.permission:plans.edit')->only(['edit', 'update', 'toggleStatus']);
$this->middleware('admin.permission:plans.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $plans = Plan::query()
            ->withCount('features')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->string('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.sections.plans.index', compact('plans'));
    }

    public function create()
    {
        $features = Feature::query()
            ->where('is_active', true)
            ->orderBy('module')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('module');

        return view('admin.sections.plans.create', compact('features'));
    }

    public function store(StorePlanRequest $request)
    {
        $this->planService->create($request->validated());

        return redirect()
            ->route('admin.plans.index')
            ->with('success', 'تم إنشاء الباقة بنجاح.');
    }

    public function edit(Plan $plan)
    {
        $plan->load('features');

        $features = Feature::query()
            ->where('is_active', true)
            ->orderBy('module')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('module');

        $featureValues = $plan->features
            ->mapWithKeys(fn ($feature) => [
                $feature->id => $feature->pivot->value,
            ])
            ->toArray();

        return view('admin.sections.plans.edit', compact('plan', 'features', 'featureValues'));
    }

    public function update(UpdatePlanRequest $request, Plan $plan)
    {
        $this->planService->update($plan, $request->validated());

        return redirect()
            ->route('admin.plans.index')
            ->with('success', 'تم تحديث الباقة بنجاح.');
    }

    public function destroy(Plan $plan)
    {
        if (in_array($plan->slug, ['free', 'starter', 'growth', 'pro'], true)) {
            return back()->with('error', 'لا يمكن حذف الباقات الأساسية. يمكنك تعطيلها فقط.');
        }

        $this->planService->delete($plan);

        return redirect()
            ->route('admin.plans.index')
            ->with('success', 'تم حذف الباقة بنجاح.');
    }

    public function toggleStatus(Plan $plan)
    {
        if ($plan->slug === 'free' && $plan->is_active) {
            return back()->with('error', 'لا يفضل تعطيل الخطة المجانية لأنها تستخدم كخطة افتراضية.');
        }

        $this->planService->toggleStatus($plan);

        return back()->with('success', 'تم تغيير حالة الباقة بنجاح.');
    }
}