<?php

namespace App\Http\Controllers;

use App\Http\Requests\Public\StorePublicBookingRequest;
use App\Models\Workspace;
use App\Services\Core\FeatureLimitService;
use App\Services\Public\PublicBookingService;

class PublicBookingController extends Controller
{
    public function __construct(
        private readonly PublicBookingService $publicBookingService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function create(Workspace $workspace)
    {
        abort_if($workspace->status !== 'active', 404);

        $workspace->load('businessProfile');

        $profile = $workspace->businessProfile;

        abort_if(! $profile || ! $profile->is_published, 404);

        $bookingEnabled = $this->featureLimitService->enabled($workspace, 'booking_enabled');

        abort_if(! $bookingEnabled, 403, 'الحجز غير متاح في الباقة الحالية.');

        $services = $workspace->businessServices()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

            $workspace->loadMissing('businessSettings');

$settings = [
    'booking_days' => explode(',', $workspace->getSetting('booking_days', 'sat,sun,mon,tue,wed,thu')),
    'booking_start_time' => $workspace->getSetting('booking_start_time', '10:00'),
    'booking_end_time' => $workspace->getSetting('booking_end_time', '22:00'),
    'booking_slot_interval' => (int) $workspace->getSetting('booking_slot_interval', 30),
    'booking_advance_days' => (int) $workspace->getSetting('booking_advance_days', 14),
];

        return view('public.booking.create', compact('workspace', 'profile', 'services', 'settings'));
    }

    public function store(StorePublicBookingRequest $request, Workspace $workspace)
    {
        abort_if($workspace->status !== 'active', 404);

        $bookingEnabled = $this->featureLimitService->enabled($workspace, 'booking_enabled');

        abort_if(! $bookingEnabled, 403, 'الحجز غير متاح في الباقة الحالية.');

        $appointmentsLimit = $this->featureLimitService->limit($workspace, 'appointments_limit', 10);

        if ($appointmentsLimit !== -1) {
            $currentCount = $workspace->businessAppointments()->count();

            if ($currentCount >= $appointmentsLimit) {
                return back()
                    ->withInput()
                    ->with('error', 'لا يمكن استقبال حجوزات جديدة حاليًا.');
            }
        }


        // /////////////////////////////////////////////////////////
        $workspace->loadMissing('businessSettings');

$allowedDays = explode(',', $workspace->getSetting('booking_days', 'sat,sun,mon,tue,wed,thu'));

$dayMap = [
    0 => 'sun',
    1 => 'mon',
    2 => 'tue',
    3 => 'wed',
    4 => 'thu',
    5 => 'fri',
    6 => 'sat',
];

$date = \Carbon\Carbon::parse($request->input('appointment_date'));
$dayKey = $dayMap[$date->dayOfWeek];

if (! in_array($dayKey, $allowedDays, true)) {
    return back()
        ->withInput()
        ->with('error', 'هذا اليوم غير متاح للحجز.');
}

$start = $request->input('start_time');
$bookingStart = $workspace->getSetting('booking_start_time', '10:00');
$bookingEnd = $workspace->getSetting('booking_end_time', '22:00');

if ($start < $bookingStart || $start >= $bookingEnd) {
    return back()
        ->withInput()
        ->with('error', 'هذا الوقت خارج ساعات العمل.');
}
        // /////////////////////////////////////////////////////////

        // $appointment = $this->publicBookingService->createBooking(
        //     workspace: $workspace,
        //     data: $request->validated()
        // );

        try {
    $appointment = $this->publicBookingService->createBooking(
        workspace: $workspace,
        data: $request->validated()
    );

    return redirect()
        ->route('public.booking.success', $workspace)
        ->with('appointment_id', $appointment->id);
} catch (\Throwable $e) {
    return back()
        ->withInput()
        ->with('error', $e->getMessage());
}


    }

    public function success(Workspace $workspace)
    {
        $workspace->load('businessProfile');

        $profile = $workspace->businessProfile;

        abort_if(! $profile || ! $profile->is_published, 404);

        return view('public.booking.success', compact('workspace', 'profile'));
    }
}