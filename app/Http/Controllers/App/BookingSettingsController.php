<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\UpdateBookingSettingsRequest;
use App\Models\Workspace;
use App\Services\App\BookingSettingsService;

class BookingSettingsController extends Controller
{
    public function __construct(
        private readonly BookingSettingsService $bookingSettingsService
    ) {}

    public function edit(Workspace $workspace)
    {
        $settings = $this->bookingSettingsService->defaults($workspace);

        return view('app.booking-settings.edit', compact('workspace', 'settings'));
    }

    public function update(UpdateBookingSettingsRequest $request, Workspace $workspace)
    {
        $this->bookingSettingsService->update(
            workspace: $workspace,
            data: $request->validated()
        );

        return redirect()
            ->route('app.booking-settings.edit', $workspace)
            ->with('success', 'تم تحديث إعدادات الحجز بنجاح.');
    }
}