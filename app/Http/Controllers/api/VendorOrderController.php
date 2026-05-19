<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\VendorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorOrderController extends Controller
{
    public function index(Request $request)
    {
        $vendorProfile = VendorProfile::where('user_id', auth()->id())->firstOrFail();

        $orders = Order::where('vendor_id', $vendorProfile->id)
            ->with('items',
            
            'driver',                    // عشان الاسم / الموبايل
            'driver.driverProfile',   
            )
            ->orderByDesc('id')
            ->get();

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $vendorProfile = VendorProfile::where('user_id', auth()->id())->firstOrFail();

        $data = $request->validate([
            'notes'         => 'nullable|string',
            'items'         => 'required|array|min:1',
            // 'items.*.client_name'      => 'nullable|string|max:255',
            // 'items.*.client_phone'     => 'nullable|string|max:50',
            'items.*.region_letter'    => 'required|string|max:50',
            'items.*.building_number'  => 'required|string|max:50',
            // 'items.*.floor'            => 'nullable|string|max:50',
            // 'items.*.apartment'        => 'nullable|string|max:50',
            // 'items.*.amount_to_collect'=> 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($data, $vendorProfile) {

            // $totalAmount = collect($data['items'])->sum('amount_to_collect');
            $bagCount    = count($data['items']);

            $order = Order::create([
                'code'           => 'ORD-' . now()->format('YmdHis'),
                'vendor_id'      => $vendorProfile->id,
                'status'         => 'pending',
                'pickup_address' => $vendorProfile->pickup_address,
                'notes'          => $data['notes'] ?? null,
                'total_amount'   => $totalAmount??0,
                'bag_count'      => $bagCount,
            ]);

            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id'        => $order->id,
                    'client_name'     => $item['client_name'] ?? null,
                    'client_phone'    => $item['client_phone'] ?? null,
                    'region_letter'   => $item['region_letter'],
                    'building_number' => $item['building_number'],
                    'floor'           => $item['floor'] ?? null,
                    'apartment'       => $item['apartment'] ?? null,
                    'amount_to_collect'=> $item['amount_to_collect']??0,
                ]);
            }

               $fcm = app(\App\Services\FcmService::class);

            //    send to vendor
    $vendorUser = $order->vendor?->user; // تأكد إن عندك علاقة vendor->user
    if ($vendorUser && $vendorUser->fcm_token) {
        $fcm->sendToToken($vendorUser->fcm_token, [
            'action'   => 'order_accepted',
            'order_id' => (string) $order->id,
            'head'     => 'تم قبول طلبك',
            'desc'     => "تم إضافة طلب رقم {$order->code} وسيتم القبول قريبًا.",
        ]);
    }
    // send to driver (all active drivers)
    $activeDrivers = \App\Models\DriverProfile::where('is_active', 1)->with('user')->get();
    foreach ($activeDrivers as $driverProfile) {
        $driverUser = $driverProfile->user;
        if ($driverUser && $driverUser->fcm_token) {
            $fcm->sendToToken($driverUser->fcm_token, [
                'action'   => 'new_order_available',
                'order_id' => (string) $order->id,  
                'head'     => 'طلب جديد متاح',
                'desc'     => "تم إضافة طلب رقم {$order->code} وهو الآن متاح للسائقين.",
            ]);
        }
    }
    
            return response()->json([
                'status' => 'success',
                'order'  => $order->load('items'),
            ], 201);
        });
    }

   public function show(Order $order)
{
    $vendorProfile = VendorProfile::where('user_id', auth()->id())->firstOrFail();

    if ($order->vendor_id !== $vendorProfile->id) {
        abort(403, 'Unauthorized');
    }

    $order->load([
        'items',
        'driver',
        'driver.driverProfile',
    ]);

    return response()->json([
        'status' => 'success',
        'order'  => $order,
    ]);
}


    public function cancel(Request $request, Order $order)
{
    $user = Auth::guard('api')->user();

    if (!$user || $user->type !== 'vendor') {
        return response()->json([
            'status'  => 'error',
            'message' => 'غير مصرح.',
        ], 403);
    }

    // هنا نستخدم العلاقة الصحيحة: vendorProfile
    // مع دعم أي اسم آخر لو موجود (vendor_profile)
    $vendorProfile = $user->vendor_profile ?? $user->vendorProfile ?? null;

    if (!$vendorProfile) {
        return response()->json([
            'status'  => 'error',
            'message' => 'لا يوجد بروفايل تاجر مربوط بهذا المستخدم.',
        ], 403);
    }

    // هنا يتحقق إن الـ vendor_id في الطلب يساوي id للبروفايل
    if ((int) $order->vendor_id !== (int) $vendorProfile->id) {
        return response()->json([
            'status'  => 'error',
            'message' => 'هذا الطلب غير تابع لك.',
        ], 403);
    }

    // لا يمكن إلغاء طلب بعد بدأ التوصيل أو بعد اكتماله
    if (!in_array($order->status, ['pending', 'accepted'])) {
        return response()->json([
            'status'  => 'error',
            'message' => 'لا يمكن إلغاء هذا الطلب في حالته الحالية.',
        ], 422);
    }

    $request->validate([
        'reason' => 'nullable|string|max:2000',
    ]);
    $reason = $request->input('reason');

    $order->update([
        'status'      => 'canceled',
        'canceled_at' => now(),
    ]);

    $order->items()->update([
        'status' => 'canceled',
    ]);

    $order->load(['vendor', 'items']);

    // send notification to driver if assigned
    if ($order->driver && $order->driver->user && $order->driver->user->fcm_token) {
        $fcm = app(\App\Services\FcmService::class);
        $fcm->sendToToken($order->driver->user->fcm_token, [
            'action'   => 'order_canceled',
            'order_id' => (string) $order->id,
            'head'     => 'تم إلغاء الطلب',
            'desc'     => "تم إلغاء طلب رقم {$order->code} من قبل التاجر.",
        ]);
    }
    return response()->json([
        'status'  => 'success',
        'message' => $reason
            ? 'تم إلغاء الطلب بنجاح. سبب الإلغاء: ' . $reason
            : 'تم إلغاء الطلب بنجاح.',
        'order'   => $order,
    ]);
}

}
