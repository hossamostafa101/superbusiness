<?php

namespace App\Services\App;

use App\Models\BusinessAppointment;
use App\Models\BusinessService;
use App\Models\Workspace;
use Carbon\Carbon;

class AppointmentAvailabilityService
{
    public function calculateEndTime(string $startTime, ?BusinessService $service = null, ?string $endTime = null): string
    {
        if ($endTime) {
            return $endTime;
        }

        $duration = $service?->duration_minutes ?? 30;

        return Carbon::createFromFormat('H:i', $startTime)
            ->addMinutes($duration)
            ->format('H:i');
    }

    public function hasConflict(
        Workspace $workspace,
        string $appointmentDate,
        string $startTime,
        string $endTime,
        ?int $ignoreAppointmentId = null
    ): bool {
        return BusinessAppointment::query()
            ->where('workspace_id', $workspace->id)
            ->whereDate('appointment_date', $appointmentDate)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->when($ignoreAppointmentId, function ($query) use ($ignoreAppointmentId) {
                $query->where('id', '!=', $ignoreAppointmentId);
            })
            ->where(function ($query) use ($startTime, $endTime) {
                $query
                    ->where(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                            ->where('end_time', '>', $startTime);
                    })
                    ->orWhere(function ($q) use ($startTime, $endTime) {
                        $q->whereNull('end_time')
                            ->where('start_time', '>=', $startTime)
                            ->where('start_time', '<', $endTime);
                    });
            })
            ->exists();
    }

    public function ensureAvailable(
        Workspace $workspace,
        string $appointmentDate,
        string $startTime,
        string $endTime,
        ?int $ignoreAppointmentId = null
    ): void {
        if ($this->hasConflict($workspace, $appointmentDate, $startTime, $endTime, $ignoreAppointmentId)) {
            throw new \RuntimeException('هذا الموعد غير متاح، يوجد موعد آخر في نفس الوقت.');
        }
    }
}