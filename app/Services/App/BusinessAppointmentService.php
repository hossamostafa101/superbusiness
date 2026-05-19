<?php

namespace App\Services\App;

use App\Models\BusinessAppointment;
use App\Models\Workspace;

class BusinessAppointmentService
{
    public function __construct(
        private readonly AppointmentAvailabilityService $availabilityService
    ) {}

    public function create(Workspace $workspace, array $data): BusinessAppointment
    {
        $payload = $this->preparePayload($workspace, $data);

        $this->availabilityService->ensureAvailable(
            workspace: $workspace,
            appointmentDate: $payload['appointment_date'],
            startTime: $payload['start_time'],
            endTime: $payload['end_time']
        );

        return $workspace->businessAppointments()->create($payload);
    }

    public function update(BusinessAppointment $appointment, Workspace $workspace, array $data): BusinessAppointment
    {
        $payload = $this->preparePayload($workspace, $data);

        $this->availabilityService->ensureAvailable(
            workspace: $workspace,
            appointmentDate: $payload['appointment_date'],
            startTime: $payload['start_time'],
            endTime: $payload['end_time'],
            ignoreAppointmentId: $appointment->id
        );

        $appointment->update($payload);

        return $appointment;
    }

    public function delete(BusinessAppointment $appointment): void
    {
        $appointment->delete();
    }

    private function preparePayload(Workspace $workspace, array $data): array
    {
        $customer = null;

        if (! empty($data['customer_id'])) {
            $customer = $workspace->businessCustomers()
                ->where('id', $data['customer_id'])
                ->first();
        }

        $service = null;

        if (! empty($data['service_id'])) {
            $service = $workspace->businessServices()
                ->where('id', $data['service_id'])
                ->first();
        }

        $startTime = $data['start_time'];

        $endTime = $this->availabilityService->calculateEndTime(
            startTime: $startTime,
            service: $service,
            endTime: $data['end_time'] ?? null
        );

        return [
            'customer_id' => $customer?->id,
            'service_id' => $service?->id,

            'customer_name' => $customer?->name ?? ($data['customer_name'] ?? null),
            'customer_phone' => $customer?->phone ?? ($data['customer_phone'] ?? null),
            'customer_email' => $customer?->email ?? ($data['customer_email'] ?? null),

            'appointment_date' => $data['appointment_date'],
            'start_time' => $startTime,
            'end_time' => $endTime,

            'status' => $data['status'] ?? 'pending',
            'source' => $data['source'] ?? 'manual',
            'notes' => $data['notes'] ?? null,
        ];
    }
}