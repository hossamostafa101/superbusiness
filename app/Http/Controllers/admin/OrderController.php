<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Driver;
use App\Models\DriverProfile;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['vendor', 'driver'])
            ->latest();

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($driverId = $request->get('driver_id')) {
            $query->where('driver_id', $driverId);
        }

        $orders = $query->paginate(30);
        $drivers = DriverProfile::orderBy('name')->get();

        return view('admin.sections.orders.index', compact('orders', 'drivers'));
    }

    public function show(Order $order)
    {
        $order->load(['vendor', 'driver', 'items']); // items = الشنط
        return view('admin.sections.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        // تعديل حالة الطلب أو إسناد سائق
        $data = $request->validate([
            'status'    => 'nullable|string|in:pending,assigned,picked_up,delivering,delivered,canceled',
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        $order->fill($data);
        $order->save();

        return redirect()
            ->route('admin.sections.orders.show', $order)
            ->with('success', 'تم تحديث الطلب.');
    }

    public function destroy(Order $order)
    {
        // غالباً مش هتحب تحذف طلب، ممكن بس تعمل soft delete
        // لسه مش هنستخدمها، عشان كده عملنا resource فقط index/show/update
        abort(404);
    }

      public function forceAssign(Request $request, Order $order)
    {
        // if (! auth()->check() || ! auth()->user()->isAdmin()) {
        //     abort(403);
        // }

        $data = $request->validate([
            'driver_id' => ['required', 'exists:users,id'],
        ]);

        // لو حابب تمنع تغيير السائق لو الطلب له سائق أصلاً:
        // if ($order->driver_id && $order->driver_id != $data['driver_id']) {
        //     return back()->with('error', 'الطلب مُسند بالفعل إلى سائق آخر.');
        // }

        $order->driver_id = $data['driver_id'];

        // لو الطلب لسه في pending/accepted خليه يعتبر مَسند
        if (in_array($order->status, ['pending', 'accepted'])) {
            $order->status = 'accepted'; // أو 'assigned' لو عندك الحالة دي
        }

        $order->save();

        // (اختياري) هنا ممكن تبعت FCM للسائق الجديد إن فيه طلب تم إسناده له

        return back()->with('success', 'تم إسناد الطلب إلى السائق بنجاح.');
    }
}
