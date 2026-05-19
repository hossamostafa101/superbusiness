<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverOrderController extends Controller
{
    public function available()
    {
        $driver = Auth::guard('api')->user();
        // send the order if is_active == 1
        if (!$driver->driverProfile || $driver->type !== 'driver' || !$driver->driverProfile->is_active) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }
        $orders = Order::whereNull('driver_id')
            ->where('status', 'pending')
            ->with('vendor') // هنعمل relation
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($orders);
    }

    public function current(Request $request)
    {
        $driver = Auth::guard('api')->user();

        if (!$driver || $driver->type !== 'driver') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $orders = Order::where('driver_id', $driver->id)
            ->whereIn('status', ['accepted', 'picked']) // أو ['accepted','picked','delivering'] لو عندك
            ->with(['vendor', 'items'])
            ->orderByDesc('accepted_at')
            ->get();

        return response()->json([
            'status' => 'success',
            'orders' => $orders,
        ]);
    }

    public function accept(Order $order, Request $request)
{
    $driver = Auth::guard('api')->user(); // أو guard السائق عندك

    // لو الإردر متسنّد لسائق تاني
    if ($order->driver_id && $order->driver_id !== $driver->id) {
        return response()->json([
            'status'  => 'error',
            'message' => 'تم إسناد هذا الطلب لسائق آخر بالفعل.',
        ], 409);
    }

     if ($order->status === 'canceled') {
        return response()->json([
            'status'  => 'error',
            'message' => 'تم إلغاء هذا الطلب من قِبل المورّد.',
        ], 422);
    }

    // لازم يكون pending عشان يتقبل
    if ($order->status !== 'pending') {
        return response()->json([
            'status'  => 'error',
            'message' => 'لا يمكن قبول هذا الطلب في حالته الحالية.',
        ], 422);
    }
    

    $order->update([
        'driver_id'   => $driver->id,
        'status'      => 'accepted',
        'accepted_at' => now(),
    ]);

    // هات كل العلاقات اللي محتاجها الموبايل
    $order->load(['vendor', 'items']);

    $fcm = app(\App\Services\FcmService::class);

    $vendorUser = $order->vendor?->user; // تأكد إن عندك علاقة vendor->user
    if ($vendorUser && $vendorUser->fcm_token) {
        $fcm->sendToToken($vendorUser->fcm_token, [
            'action'   => 'order_accepted',
            'order_id' => (string) $order->id,
            'head'     => 'تم قبول طلبك',
            'desc'     => "السائق قبل طلب رقم {$order->code} وسيتم الاستلام قريبًا.",
        ]);
    }

    return response()->json([
        'status'  => 'success',
        'message' => 'تم قبول الطلب بنجاح.',
        'order'   => $order,
    ]);
}


public function pickup(Order $order)
{
    $this->checkDriverOwnsOrder($order);

    // مسموح فقط لو لسه مقبول ومش متسلَّم


    
 if ($order->status === 'canceled') {
        return response()->json([
            'status'  => 'error',
            'message' => 'تم إلغاء هذا الطلب من قِبل المورّد.',
        ], 422);
    }

    if ($order->status !== 'accepted') {
        return response()->json([
            'status'  => 'error',
            'message' => 'لا يمكن تغيير حالة هذا الطلب إلى تم الاستلام من المورد.',
        ], 422);
    }

    $order->update([
        'status'    => 'picked',
        'picked_at' => now(),
    ]);

    $order->load(['vendor', 'items']);

    return response()->json([
        'status'  => 'success',
        'message' => 'تم تسجيل استلام الطلب من المورد.',
        'order'   => $order,
    ]);
}

public function complete(Order $order)
{
    $this->checkDriverOwnsOrder($order);

    // هنا نسمح لو في حالة picked (أو accepted لو حابب)
    if (! in_array($order->status, ['picked', 'accepted'], true)) {
        return response()->json([
            'status'  => 'error',
            'message' => 'لا يمكن إنهاء هذا الطلب في حالته الحالية.',
        ], 422);
    }

    if ($order->status === 'canceled') {
        return response()->json([
            'status'  => 'error',
            'message' => 'تم إلغاء هذا الطلب من قِبل المورّد.',
        ], 422);
    }
    
    $order->update([
        'status'       => 'completed',
        'completed_at' => now(),
    ]);

    // ممكن تحدث items -> delivered هنا لو عايز
    // $order->items()->update([...]);

    $order->load(['vendor', 'items']);

    return response()->json([
        'status'  => 'success',
        'message' => 'تم إنهاء الطلب وتسليم جميع الشنط.',
        'order'   => $order,
    ]);
}

public function history(Request $request)
{
    $driver = Auth::guard('api')->user();

    if (!$driver || $driver->type !== 'driver') {
        return response()->json([
            'status'  => 'error',
            'message' => 'Unauthorized',
        ], 401);
    }

    $orders = Order::where('driver_id', $driver->id)
        ->whereIn('status', ['completed', 'canceled']) // تاريخ = خلصت أو اتلغت
        ->with(['vendor', 'items'])
        ->orderByDesc('completed_at')
        ->orderByDesc('updated_at')
        ->get();

    return response()->json([
        'status' => 'success',
        'orders' => $orders,
    ]);
}



    // public function pickup(Order $order)
    // {
    //     $this->checkDriverOwnsOrder($order);

    //     $order->update([
    //         'status'    => 'picked',
    //         'picked_at' => now(),
    //     ]);

    //     return response()->json($order->fresh('items'));
    // }

    // public function complete(Order $order)
    // {
    //     $this->checkDriverOwnsOrder($order);

    //     $order->update([
    //         'status'       => 'completed',
    //         'completed_at' => now(),
    //     ]);

    //     // ممكن تحدث order_items لـ delivered هنا لو حابب

    //     return response()->json($order->fresh('items'));
    // }

    protected function checkDriverOwnsOrder(Order $order)
    {
        $user = auth()->user();

        if ($user->type !== 'driver' || $order->driver_id !== $user->id) {
            abort(403, 'Unauthorized');
        }
    }
}
