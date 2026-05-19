<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\ManualPaymentRequest;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RestaurantPlanController extends Controller
{
    /**
     * عرض الخطط المتاحة للمطاعم لكي يختار منها بعد التسجيل/الدخول.
     */
    public function index()
    {
        $user = Auth::user();

        if (! $user || ! $user->isRestaurantAccount()) {
            abort(403);
        }

        // نفترض إن الحساب مرتبط بمطعم واحد (مالك)
        $restaurant = $user->restaurants()
            ->with(['activeSubscription.plan'])
            ->firstOrFail();

        $plans = Plan::query()
            ->where('is_active', true)
            ->orderBy('price')
            ->get();

        // طلبات الدفع اليدوي قيد المراجعة للمطعم
        $pendingRequests = ManualPaymentRequest::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('status', ManualPaymentRequest::STATUS_PENDING)
            ->get()
            ->groupBy('plan_id'); // collection keyed by plan_id

        return view('restaurant.sections.plans.index', [
            'user'            => $user,
            'restaurant'      => $restaurant,
            'plans'           => $plans,
            'pendingRequests' => $pendingRequests,
        ]);
    }


     /**
     * صفحة الـ checkout للخطة (عرض أرقام InstaPay / Vodafone + رفع الإيصال)
     */
    public function showCheckout(Plan $plan)
    {
        $user = Auth::user();

        if (! $user || ! $user->isRestaurantAccount()) {
            abort(403);
        }

        // لحد دلوقتي هنفترض إن كل Account عنده مطعم واحد
        $restaurant = $user->restaurants()->firstOrFail();

        $manualMethods = config('payments.manual');

        return view('restaurant.sections.plans.checkout', [
            'user'          => $user,
            'restaurant'    => $restaurant,
            'plan'          => $plan,
            'manualMethods' => $manualMethods, // عشان نعرض الأرقام في الـ Blade
        ]);
    }

    /**
     * استلام طلب الدفع اليدوي (رفع صورة الإيصال + بيانات المرسل)
     */
    public function submitManualPayment(Request $request, Plan $plan)
    {
        $user = Auth::user();

        if (! $user || ! $user->isRestaurantAccount()) {
            abort(403);
        }

        $restaurant = $user->restaurants()->firstOrFail();

        $allowedMethods = [
            ManualPaymentRequest::METHOD_INSTAPAY,
            ManualPaymentRequest::METHOD_VODAFONE_CASH,
            ManualPaymentRequest::METHOD_WALLET_CASH,
        ];

        $data = $request->validate([
            'method'        => ['required', Rule::in($allowedMethods)],
            'sender_name'   => ['nullable', 'string', 'max:150'],
            'sender_phone'  => ['nullable', 'string', 'max:30'],
            'receipt_image' => ['required', 'image', 'max:5120'], // 5MB
        ]);

        // رفع صورة الإيصال إلى storage/app/public/manual_payments
        $path = $request->file('receipt_image')->store('manual_payments', 'public');

        ManualPaymentRequest::create([
            'restaurant_id'      => $restaurant->id,
            'plan_id'            => $plan->id,
            'subscription_id'    => null,
            'method'             => $data['method'],
            'amount_expected'    => $plan->price,
            'sender_name'        => $data['sender_name'] ?? null,
            'sender_phone'       => $data['sender_phone'] ?? null,
            'receipt_image_path' => $path,
            'status'             => ManualPaymentRequest::STATUS_PENDING,
        ]);

        return redirect()
            ->route('restaurant.plans.index')
            ->with('success', 'تم إرسال طلب الدفع بنجاح، سيتم مراجعة التحويل من قِبل الإدارة في أقرب وقت.');
    }
}
