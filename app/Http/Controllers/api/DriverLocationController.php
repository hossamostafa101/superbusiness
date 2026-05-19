<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverLocationController extends Controller
{
    public function ping(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user || $user->type !== 'driver') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $data = $request->validate([
            'lat'      => 'required|numeric',
            'lng'      => 'required|numeric',
            'order_id' => 'nullable|integer|exists:orders,id',
            'reason'   => 'nullable|string|max:2000',
        ]);

        $order = null;

        if (!empty($data['order_id'])) {
            // نتأكد إن الطلب فعلاً تابع للسائق ده
            $order = Order::where('id', $data['order_id'])
                ->where('driver_id', $user->id)
                ->first();
        }

        if ($order) {
            $order->update([
                'driver_last_lat'         => $data['lat'],
                'driver_last_lng'         => $data['lng'],
                'driver_last_location_at' => now(),
            ]);
        }

        // لو حابب تسجّل في جدول منفصل history تعمل موديل تاني هنا

        return response()->json([
            'status'  => 'success',
            'message' => 'Location stored successfully',
        ]);
    }

      public function updateLocation(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (! $user || $user->type !== 'driver') {
            return response()->json([
                'status'  => 'error',
                'message' => 'غير مصرح.',
            ], 403);
        }

        $data = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $driverProfile = $user->driver_profile ?? $user->driverProfile ?? null;

        if (! $driverProfile) {
            return response()->json([
                'status'  => 'error',
                'message' => 'لا يوجد بروفايل سائق مربوط بهذا المستخدم.',
            ], 404);
        }

        $driverProfile->update([
            'driver_last_lat'         => $data['lat'],
            'driver_last_lng'         => $data['lng'],
            'driver_last_location_at' => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'تم تحديث موقع السائق.',
        ]);
    }
}
