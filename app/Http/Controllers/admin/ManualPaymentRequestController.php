<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ManualPaymentRequest;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManualPaymentRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ManualPaymentRequest::with(['restaurant', 'plan', 'subscription', 'reviewer'])
            ->orderByDesc('id');

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $requests = $query->paginate(20)->withQueryString();

        return view('admin.sections.manual_payments.index', compact('requests'));
    }

    public function show(ManualPaymentRequest $manualPaymentRequest)
    {
        $manualPaymentRequest->load(['restaurant', 'plan', 'subscription', 'reviewer']);

        return view('admin.sections.manual_payments.show', [
            'request' => $manualPaymentRequest,
        ]);
    }

    public function approve(Request $request, ManualPaymentRequest $manualPaymentRequest)
    {
        if (! $manualPaymentRequest->isPending()) {
            return back()->withErrors(['action' => 'لا يمكن تعديل حالة هذا الطلب بعد مراجعته مسبقًا.']);
        }

        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:255'],
        ]);

        $admin = Auth::user();

        DB::transaction(function () use ($manualPaymentRequest, $admin, $data) {

            // لو لسه مفيش Subscription مربوط بالطلب، نعمل واحد جديد
            if (! $manualPaymentRequest->subscription_id) {

                $plan       = $manualPaymentRequest->plan;
                $restaurant = $manualPaymentRequest->restaurant;

                $startsAt = now();
                $endsAt   = $plan->duration_days
                    ? now()->copy()->addDays($plan->duration_days)
                    : null;

                /** @var \App\Models\Subscription $subscription */
                $subscription = Subscription::create([
                    'restaurant_id' => $restaurant->id,
                    'plan_id'       => $plan->id,
                    'status'        => 'active',
                    'starts_at'     => $startsAt,
                    'ends_at'       => $endsAt,
                    'activated_by'  => $admin->id,
                    'activated_at'  => now(),
                ]);

                $manualPaymentRequest->markApproved($admin, $subscription, $data['admin_note'] ?? null);
            } else {
                // في حالة إن فيه اشتراك موجود بالفعل، نحدّث حالته ونعلم الطلب كمقبول
                $subscription = $manualPaymentRequest->subscription;

                if ($subscription && $subscription->status !== 'active') {
                    $subscription->update([
                        'status'       => 'active',
                        'activated_by' => $admin->id,
                        'activated_at' => now(),
                    ]);
                }

                $manualPaymentRequest->markApproved($admin, $subscription, $data['admin_note'] ?? null);
            }
        });

        return redirect()
            ->route('admin.manual-payments.index')
            ->with('success', 'تم قبول الدفع وتفعيل اشتراك المطعم.');
    }

    public function reject(Request $request, ManualPaymentRequest $manualPaymentRequest)
    {
        if (! $manualPaymentRequest->isPending()) {
            return back()->withErrors(['action' => 'لا يمكن تعديل حالة هذا الطلب بعد مراجعته مسبقًا.']);
        }

        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:255'],
        ]);

        $admin = Auth::user();

        $manualPaymentRequest->markRejected($admin, $data['admin_note'] ?? null);

        return redirect()
            ->route('admin.manual-payments.index')
            ->with('success', 'تم رفض الطلب وتسجيل الملاحظة.');
    }
}
