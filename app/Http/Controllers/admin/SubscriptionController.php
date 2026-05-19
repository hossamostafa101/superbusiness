<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubscriptionRequest;
use App\Http\Requests\Admin\UpdateSubscriptionRequest;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Workspace;
use App\Services\Admin\SubscriptionAdminService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        private readonly SubscriptionAdminService $subscriptionService
    ) {
        $this->middleware('permission:subscriptions.view')->only(['index']);
        $this->middleware('permission:subscriptions.create')->only(['create', 'store']);
        $this->middleware('permission:subscriptions.edit')->only(['edit', 'update', 'markActive']);
        $this->middleware('permission:subscriptions.cancel')->only(['cancel', 'destroy']);
    }

    public function index(Request $request)
    {
        $subscriptions = Subscription::query()
            ->with([
                'workspace:id,name,slug,owner_id,status',
                'workspace.owner:id,name,email',
                'plan:id,name,slug,currency,monthly_price,yearly_price',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->whereHas('workspace', function ($workspaceQuery) use ($search) {
                    $workspaceQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                            $ownerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('billing_cycle'), function ($query) use ($request) {
                $query->where('billing_cycle', $request->input('billing_cycle'));
            })
            ->when($request->filled('plan_id'), function ($query) use ($request) {
                $query->where('plan_id', $request->input('plan_id'));
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $plans = Plan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);

        return view('admin.sections.subscriptions.index', compact('subscriptions', 'plans'));
    }

    public function create()
    {
        $workspaces = Workspace::query()
            ->with('owner:id,name,email')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'owner_id']);

        $plans = Plan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'monthly_price', 'yearly_price', 'currency']);

        return view('admin.sections.subscriptions.create', compact('workspaces', 'plans'));
    }

    public function store(StoreSubscriptionRequest $request)
    {
        $this->subscriptionService->create($request->validated());

        return redirect()
            ->route('admin.subscriptions.index')
            ->with('success', 'تم إنشاء الاشتراك بنجاح.');
    }

    public function edit(Subscription $subscription)
    {
        $subscription->load(['workspace.owner', 'plan']);

        $plans = Plan::query()
            ->where('is_active', true)
            ->orWhere('id', $subscription->plan_id)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'monthly_price', 'yearly_price', 'currency']);

        return view('admin.sections.subscriptions.edit', compact('subscription', 'plans'));
    }

    public function update(UpdateSubscriptionRequest $request, Subscription $subscription)
    {
        $this->subscriptionService->update($subscription, $request->validated());

        return redirect()
            ->route('admin.subscriptions.index')
            ->with('success', 'تم تحديث الاشتراك بنجاح.');
    }

    public function destroy(Subscription $subscription)
    {
        $this->subscriptionService->delete($subscription);

        return redirect()
            ->route('admin.subscriptions.index')
            ->with('success', 'تم حذف الاشتراك بنجاح.');
    }

    public function cancel(Subscription $subscription)
    {
        $this->subscriptionService->cancel($subscription);

        return back()->with('success', 'تم إلغاء الاشتراك بنجاح.');
    }

    public function markActive(Subscription $subscription)
    {
        $this->subscriptionService->markActive($subscription);

        return back()->with('success', 'تم تفعيل الاشتراك بنجاح.');
    }
}