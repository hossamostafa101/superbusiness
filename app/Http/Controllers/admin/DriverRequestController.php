<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DriverProfile;
use Illuminate\Http\Request;

class DriverRequestController extends Controller
{
    // قائمة طلبات السائقين المعلقة
    public function index()
    {
        $drivers = DriverProfile::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.sections.driver_requests.index', compact('drivers'));
    }

    // قائمة السائقين المقبولين (اختياري)
    public function approvedIndex()
    {
        $drivers = DriverProfile::with('user')
            ->where('status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->paginate(20);

        return view('admin.sections.driver_requests.approved', compact('drivers'));
    }

    // عرض تفاصيل طلب واحد
    public function show(DriverProfile $driverProfile)
    {
        $driverProfile->load('user');

        return view('admin.sections.driver_requests.show', [
            'driver' => $driverProfile,
        ]);
    }

    // قبول طلب سائق
    public function approve(DriverProfile $driverProfile, Request $request)
    {
        if ($driverProfile->status === 'approved') {
            return redirect()
                ->back()
                ->with('info', 'تم قبول هذا السائق مسبقًا.');
        }

        $driverProfile->update([
            'status'            => 'approved',
            'approved_at'       => now(),
            'rejected_at'       => null,
            'rejection_reason'  => null,
            'is_active'         => 1,
        ]);

        // تأكيد نوع المستخدم كسائق
        if ($driverProfile->user) {
            $driverProfile->user->update([
                'type'  => 'driver', // انت مستخدم type = 'driver' في الأوث
                'title' => 'driver',
            ]);
        }

        return redirect()
            ->route('admin.driver_requests.index')
            ->with('success', 'تم قبول طلب السائق بنجاح.');
    }

    // رفض طلب سائق
    public function reject(DriverProfile $driverProfile, Request $request)
    {
        $data = $request->validate([
            'reason' => 'nullable|string|max:2000',
        ]);

        $driverProfile->update([
            'status'           => 'rejected',
            'rejected_at'      => now(),
            'rejection_reason' => $data['reason'] ?? null,
            'is_active'        => 0,
        ]);

        return redirect()
            ->route('admin.driver_requests.index')
            ->with('success', 'تم رفض طلب السائق.');
    }
}
