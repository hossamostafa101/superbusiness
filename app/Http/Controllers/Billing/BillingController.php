<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\CheckoutRequest;
use App\Models\Plan;
use App\Models\Workspace;
use App\Services\Billing\BillingService;

class BillingController extends Controller
{
    public function __construct(
        private readonly BillingService $billingService
    ) {}

    public function plans(Workspace $workspace)
{
    $workspace->load([
        'specification',
        'activeSubscription.plan',
    ]);

    $currentSubscription = $workspace->activeSubscription;
    $currentPlan = $currentSubscription?->plan;

    $allowedModules = match ($workspace->specificationKey()) {
        'restaurant' => ['core', 'crm', 'restaurant_menu'],
        'appointments' => ['core', 'crm', 'appointments'],
        'bio' => ['core', 'bio'],
        default => ['core'],
    };

    $plans = \App\Models\Plan::query()
        ->where('is_active', true)
        ->with(['features' => function ($query) use ($allowedModules) {
            $query->whereIn('module', $allowedModules)
                ->orderBy('sort_order');
        }])
        ->orderBy('sort_order')
        ->get();

    return view('billing.plans', compact(
        'workspace',
        'plans',
        'currentSubscription',
        'currentPlan',
        'allowedModules'
    ));
}

    public function plansX(Workspace $workspace)
    {
        $plans = Plan::query()
            ->where('is_active', true)
            ->with('features')
            ->orderBy('sort_order')
            ->get();

        $workspace->load('activeSubscription.plan');

        return view('billing.plans', compact('workspace', 'plans'));
    }

public function checkout(Workspace $workspace, string $planSlug)
{
    $plan = Plan::query()
        ->where('slug', $planSlug)
        ->where('is_active', true)
        ->with('features')
        ->firstOrFail();

    $workspace->load('activeSubscription.plan');

    return view('billing.checkout', compact('workspace', 'plan'));
}

   public function process(CheckoutRequest $request, Workspace $workspace, string $planSlug)
{
    $plan = Plan::query()
        ->where('slug', $planSlug)
        ->where('is_active', true)
        ->firstOrFail();

    $data = $request->validated();

    try {
        $payment = $this->billingService->startCheckout(
            workspace: $workspace,
            plan: $plan,
            billingCycle: $data['billing_cycle'],
            provider: $data['provider'],
            receiptImage: $request->file('receipt_image'),
            reference: $data['reference'] ?? null,
            notes: $data['notes'] ?? null,
        );

        if ($payment->provider === 'manual') {
            return redirect()
                ->route('billing.success', $workspace)
                ->with('success', 'تم إرسال إثبات الدفع بنجاح. سيتم مراجعة الدفع وتفعيل الاشتراك بعد الاعتماد.');
        }

        if ($payment->checkout_url) {
            return redirect()->away($payment->checkout_url);
        }

        return redirect()
            ->route('billing.success', $workspace)
            ->with('success', 'تم إنشاء طلب الدفع بنجاح.');
    } catch (\Throwable $e) {
        return back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}
    public function success(Workspace $workspace)
    {
        return view('billing.success', compact('workspace'));
    }

    public function cancelled(Workspace $workspace)
    {
        return view('billing.cancelled', compact('workspace'));
    }
}