<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\UserPlanPurchase;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * إرجاع قائمة الباقات المفعّلة (لشاشة اختيار الباقة في التطبيق)
     * GET /api/plans
     */
    public function index()
    {
        $plans = Plan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => $plans,
        ]);
    }

    /**
     * إرجاع تفاصيل باقة مفعّلة
     * GET /api/plans/{id}
     */
    public function show($id)
    {
        $plan = Plan::where('is_active', true)
            ->where('id', $id)
            ->firstOrFail();

        return response()->json([
            'data' => $plan,
        ]);
    }


    public function page(Request $request)
    {
        $user = $request->user();

        // ===== 1) بيانات المستخدم =====
        $userData = [
            'id'       => $user->id,
            'name'     => $user->name,
            'username' => $user->username ?? null,
            'email'    => $user->email,
            'phone'    => $user->phone ?? null,
        ];

        // ===== 2) الرصيد (balance) =====
        $purchasesQuery = UserPlanPurchase::where('user_id', $user->id)
            ->where('status', 'paid');

        $invitesPurchasedTotal = (int) (clone $purchasesQuery)->sum('invitations_total');
        $invitesAvailable      = (int) (clone $purchasesQuery)->sum('invitations_remaining');
        $invitesUsedTotal      = max($invitesPurchasedTotal - $invitesAvailable, 0);

        $lastPurchase = (clone $purchasesQuery)
            ->with('plan')
            ->latest('id')
            ->first();

        $currency = $lastPurchase->currency ?? 'SAR';

        $balance = [
            'invites_available'       => $invitesAvailable,
            'invites_purchased_total' => $invitesPurchasedTotal,
            'invites_used_total'      => $invitesUsedTotal,
            'currency'                => $currency,
        ];

        $lastPurchaseData = null;
        if ($lastPurchase) {
            $lastPurchaseData = [
                'id'                    => $lastPurchase->id,
                'plan_id'               => $lastPurchase->plan_id,
                'plan_name'             => optional($lastPurchase->plan)->name,
                'invitations_total'     => (int) $lastPurchase->invitations_total,
                'invitations_remaining' => (int) $lastPurchase->invitations_remaining,
                'amount_cents'          => (int) $lastPurchase->amount_cents,
                'currency'              => $lastPurchase->currency,
                'paid_at'               => optional($lastPurchase->paid_at)->toDateTimeString(),
            ];
        }

        // ===== 3) الباقات المتاحة =====
        $plans = Plan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function (Plan $plan) use ($currency) {
                // تنسيق مبسط للسعر
                $price = $plan->price_cents / 100;

                return [
                    'id'                 => $plan->id,
                    'name'               => $plan->name,
                    'invitations_quota'  => (int) $plan->invitations_quota,
                    'price_cents'        => (int) $plan->price_cents,
                    'price'              => $price,
                    'price_formatted'    => number_format($price, 2) . ' ' . $plan->currency,
                    'currency'           => $plan->currency,
                    'is_active'          => (bool) $plan->is_active,
                    'sort_order'         => (int) $plan->sort_order,
                    'is_recommended'     => $plan->sort_order == 2, // مثال: خلي الباقة التانية هي الموصى بها
                ];
            })
            ->values();

        return response()->json([
            'status'  => 'success',
            'message' => 'Plans page loaded successfully',
            'data'    => [
                'user'          => $userData,
                'balance'       => $balance,
                'plans'         => $plans,
                'last_purchase' => $lastPurchaseData,
            ],
        ]);
    }
}
