<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\UserPlanPurchase;
use Illuminate\Http\Request;

class UserPlanPurchaseController extends Controller
{
    /**
     * قائمة مشتريات المستخدم الحالي
     * GET /api/plan-purchases
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $purchases = UserPlanPurchase::with('plan')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'data' => $purchases,
        ]);
    }

    /**
     * إنشاء شراء جديد لباقة
     * POST /api/plan-purchases
     *
     * في الواقع الحقيقي هتربط هنا مع بوابة الدفع (HyperPay, Moyasar, Tap, PayTabs…)
     * حالياً هنعتبر إنها "مدفوعة" مباشرة أو نخلي status = pending حسب اللي يناسبك.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
            // لو محتاج معلومات إضافية زي promo_code، notes .. الخ
        ]);

        $plan = Plan::where('is_active', true)->findOrFail($validated['plan_id']);

        // هنا في الحقيقة المفروض تعمل:
        // 1) إنشاء سجل purchase بحالة pending
        // 2) تنادي بوابة الدفع وترجع URL / token للـ frontend
        // دلوقتي هنمشي بالـ Flow البسيط: نعتبرها مدفوعة مباشرة (للتطوير الأولي)

        $purchase = new UserPlanPurchase();
        $purchase->user_id               = $user->id;
        $purchase->plan_id               = $plan->id;
        $purchase->invitations_total     = $plan->invitations_quota;
        $purchase->invitations_remaining = $plan->invitations_quota;
        $purchase->amount_cents          = $plan->price_cents;
        $purchase->currency              = $plan->currency;
        $purchase->payment_provider      = 'manual'; // غيّرها لما تربط Gateway
        $purchase->payment_reference     = null;
        $purchase->status                = 'paid';   // أو 'pending' لو هتكمّل تكامل بوابة الدفع
        $purchase->paid_at               = now();

        $purchase->save();

        return response()->json([
            'message' => 'Plan purchased successfully',
            'data'    => $purchase->load('plan'),
        ], 201);
    }

    /**
     * عرض تفاصيل شراء واحد للمستخدم الحالي
     * GET /api/plan-purchases/{id}
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $purchase = UserPlanPurchase::with('plan')
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        return response()->json([
            'data' => $purchase,
        ]);
    }

    /**
     * رصيد الدعوات الحالي للمستخدم
     * GET /api/me/quota
     *
     * يرجّع:
     * - إجمالي الدعوات اللي اشتراها
     * - إجمالي المتبقي
     * - ممكن تضيف إجمالي المستخدم حتى الآن
     */
    public function quota(Request $request)
    {
        $user = $request->user();

        $query = UserPlanPurchase::where('user_id', $user->id)
            ->where('status', 'paid');

        $total = (clone $query)->sum('invitations_total');
        $remaining = (clone $query)->sum('invitations_remaining');

        return response()->json([
            'data' => [
                'total_invitations'     => (int) $total,
                'remaining_invitations' => (int) $remaining,
            ],
        ]);
    }
}
