<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrderMonitorController extends Controller
{
    public function index()
    {
        // لو عايز تتأكد إنه أدمن
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403);
        }

        $now = now();

        // 1) طلبات في الانتظار (لسه بدون سواق أو مقبولة لكن لسه مابدأتشي)
        $waiting = Order::with(['vendor', 'driver','items'])
            ->whereIn('status', ['pending', 'accepted'])
            ->orderBy('created_at', 'asc')
            ->get();

        // 2) طلبات جاري توصيلها
        $onRoute = Order::with(['vendor', 'driver','items'])
            ->where('status', 'delivering')
            ->orderBy('picked_at', 'asc')
            ->get();

        // 3) طلبات مكتملة (آخر 100 مثلاً)
        // orders for todat
        $completed = Order::with(['vendor', 'driver','items'])
            ->where('status', 'completed')
            ->whereDate('completed_at', today())
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();

        // حدود الزمن
        $maxResponseMinutes = 5;   // أقصى وقت للرد
        $maxDeliveryMinutes = 15;  // أقصى وقت للتسليم

           $availableDrivers = User::drivers()
        ->whereHas('driverProfile', function ($q) {
            $q->where('is_active', 1);          // من driver_profiles
            // لو عندك قيم للحالة (pending / approved / rejected ..)
            // استخدم القيمة اللي بتعتبرها "سائق شغال"
            $q->where('status', 'approved');    // أو 'active' حسب ما هتستعمل
        })
        ->with('driverProfile')
        ->orderBy('name')
        ->get();

        return view('admin.sections.orders.monitor', compact(
            'waiting',
            'onRoute',
            'completed',
            'now',
            'maxResponseMinutes',
            'maxDeliveryMinutes',
            'availableDrivers'
        ));
    }


      public function notifyDriver(Request $request, Order $order)
    {
        if (! Auth::check() || ! Auth::user()->is_admin) {
            abort(403);
        }

        // لازم يكون فيه سائق مربوط بالطلب
        if (! $order->driver) {
            return back()->with('error', 'لا يوجد سائق مُسند لهذا الطلب.');
        }

        // نفترض إن الـ fcm_token متخزن في users table
        $token = $order->driver->fcm_token ?? null;

        if (! $token) {
            return back()->with('error', 'لا يوجد FCM token مسجل لهذا السائق.');
        }

        // نسمح (اختياريًا) بتخصيص الرسالة من الفورم
        $data = $request->validate([
            'head'   => 'nullable|string|max:255',
            'desc'   => 'nullable|string|max:2000',
            'mtopic' => 'nullable|string|max:100',
            'action' => 'nullable|string|max:50',
            'url'    => 'nullable|string|max:500',
        ]);

        $head   = $data['head']   ?? ('تنبيه من الإدارة بخصوص الطلب ' . ($order->code ?? ('#' . $order->id)));
        $desc   = $data['desc']   ?? 'من فضلك راجع حالة الطلب وتواصل مع الإدارة إذا كان هناك أي تأخير.';
        $mtopic = $data['mtopic'] ?? 'drivers';
        $action = $data['action'] ?? 'open_current_orders';
        $url    = $data['url']    ?? '';

        $projectId   = 'quickmart-fbc37';
        $accessToken = $this->getAccessToken();

        if (! $accessToken) {
            return back()->with('error', 'فشل في الحصول على Access Token من FCM.');
        }

        $fcmUrl = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $token,
                'data'  => [
                    'head'   => $head,
                    'desc'   => $desc,
                    'mtopic' => $mtopic,
                    'action' => $action,
                    'url'    => $url,
                ],
                // لو حابب كمان notification system:
                // 'notification' => [
                //     'title' => $head,
                //     'body'  => $desc,
                // ],
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type'  => 'application/json',
        ])->post($fcmUrl, $payload);

        if (! $response->successful()) {
            return back()->with('error', 'فشل إرسال الإشعار: ' . $response->body());
        }

        return back()->with('success', 'تم إرسال الإشعار للسائق بنجاح.');
    }

    /**
     * نفس دالة getAccessToken اللي عندك للإرسال لـ FCM
     */
    protected function getAccessToken(): ?string
    {
        $keyFilePath = storage_path('app/firebase/quickmart-fbc37-firebase-adminsdk-fbsvc-f9f75f4c01.json');

        if (! file_exists($keyFilePath)) {
            return null;
        }

        $googleAuthUrl = "https://oauth2.googleapis.com/token";

        $serviceAccount = json_decode(file_get_contents($keyFilePath), true);

        $jwtHeader = base64_encode(json_encode(["alg" => "RS256", "typ" => "JWT"]));

        $iat = time();
        $exp = $iat + 3600;

        $jwtPayload = base64_encode(json_encode([
            "iss"   => $serviceAccount["client_email"],
            "scope" => "https://www.googleapis.com/auth/firebase.messaging",
            "aud"   => $googleAuthUrl,
            "exp"   => $exp,
            "iat"   => $iat,
        ]));

        $signature = "";
        openssl_sign(
            "$jwtHeader.$jwtPayload",
            $signature,
            openssl_pkey_get_private($serviceAccount["private_key"]),
            OPENSSL_ALGO_SHA256
        );
        $jwtAssertion = "$jwtHeader.$jwtPayload." . base64_encode($signature);

        $response = Http::post($googleAuthUrl, [
            "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
            "assertion"  => $jwtAssertion,
        ]);

        return $response->json()["access_token"] ?? null;
    }

     public function requestDriverLocation(Order $order, Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403);
        }

        // لازم يكون للطلب سائق وله fcm_token
        $driver = $order->driver;

        if (!$driver || empty($driver->fcm_token)) {
            return back()->with('error', 'لا يوجد سائق مرتبط بالطلب أو لا يملك FCM token.');
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return back()->with('error', 'تعذر الحصول على توكن Firebase.');
        }

        $projectId = 'quickmart-fbc37'; // زي ما عندك في كود الإشعارات

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $body = [
            "message" => [
                "token" => $driver->fcm_token,
                "data"  => [
                    "action"   => "request_location",
                    "order_id" => (string) $order->id,
                    "reason"   => "طلب موقع السائق من لوحة متابعة الطلبات",
                ],
            ],
        ];

        $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ])
            ->post($url, $body);

        if (!$response->successful()) {
            \Log::error('FCM request_location error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return back()->with('error', 'تعذر إرسال طلب الموقع للسائق.');
        }

        return back()->with('success', 'تم إرسال طلب الموقع للسائق، إذا استجاب سيتم تحديث الإحداثيات في الطلب.');
    }


     public function latest(Request $request)
    {
        if (! Auth::check() || ! Auth::user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $order = Order::with(['vendor', 'driver'])
            // لو حابب تقتصر على حالات معينة، فك الكومنت تحت وعدّل
            // ->whereIn('status', ['pending', 'accepted', 'delivering'])
            ->orderByDesc('id')
            ->first();

        if (! $order) {
            return response()->json(['exists' => false]);
        }

        return response()->json([
            'exists'        => true,
            'id'            => $order->id,
            'code'          => $order->code,
            'status'        => $order->status,
            'vendor_name'   => optional($order->vendor)->business_name,
            'driver_name'   => optional($order->driver)->name,
            'total_amount'  => $order->total_amount,
            'bag_count'     => $order->bag_count,
            'created_at'    => optional($order->created_at)->toDateTimeString(),
        ]);
    }

    
    /**
     * نفس فكرة getAccessToken اللي عندك في AdminNotifyController،
     * ممكن تنقلها هنا أو تعمل Service مشترك.
     */


    public function forceComplete(Order $order)
{
    // if (! Auth::check() || ! Auth::user()->is_admin) {
    //     abort(403);
    // }

    // ممنوع التلاعب في طلب منتهي أو ملغي
    if (in_array($order->status, ['completed', 'canceled'], true)) {
        return back()->with('error', 'هذا الطلب منتهي أو ملغي بالفعل، لا يمكن تعديله.');
    }

    // نفس منطق درايفر تقريبًا: نسمح لو الحالة pending/accepted/picked
    if (! in_array($order->status, ['pending', 'accepted', 'picked'], true)) {
        return back()->with('error', 'لا يمكن إنهاء هذا الطلب في حالته الحالية.');
    }

    // تقدر تحوطها في ترانزاكشن لو حابب
    DB::transaction(function () use ($order) {
        $order->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        // لو عندك items statuses وحابب تحدثها:
        if (method_exists($order, 'items')) {
            $order->items()->update([
                'status' => 'completed',
            ]);
        }
    });

    return back()->with('success', 'تم إنهاء الطلب يدويًا من لوحة التحكم.');
}

/**
 * إلغاء الطلب يدويًا من لوحة التحكم (admin).
 */
public function forceCancel(Request $request, Order $order)
{
    // if (! Auth::check() || ! Auth::user()->is_admin) {
    //     abort(403);
    // }

    // ممنوع إلغاء طلب منتهي بالفعل
    if ($order->status === 'completed') {
        return back()->with('error', 'لا يمكن إلغاء طلب تم إنهاؤه بالفعل.');
    }

    if ($order->status === 'canceled') {
        return back()->with('error', 'هذا الطلب ملغي بالفعل.');
    }

    // نفس منطق إلغاء المورّد تقريبًا: نسمح قبل بدء التوصيل
    if (! in_array($order->status, ['pending', 'accepted', 'picked'], true)) {
        return back()->with('error', 'لا يمكن إلغاء هذا الطلب في حالته الحالية.');
    }

    $data = $request->validate([
        'reason' => 'nullable|string|max:2000',
    ]);
    $reason = $data['reason'] ?? null;

    DB::transaction(function () use ($order) {
        $order->update([
            'status'      => 'canceled',
            'canceled_at' => now(),
            // لو عندك عمود للسبب زيه زي cancel_reason ضيفه هنا:
            // 'cancel_reason' => $reason,
        ]);

        if (method_exists($order, 'items')) {
            $order->items()->update([
                'status' => 'canceled',
            ]);
        }
    });

    return back()->with('success', 'تم إلغاء الطلب يدويًا من لوحة التحكم.');
}
   
}
