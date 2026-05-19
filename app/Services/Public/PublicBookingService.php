<?php

namespace App\Services\Public;

use App\Models\BusinessAppointment;
use App\Models\BusinessCustomer;
use App\Models\BusinessService;
use App\Models\Workspace;
use App\Services\App\AppointmentAvailabilityService;
use Illuminate\Support\Facades\DB;

class PublicBookingService
{
    public function __construct(
        private readonly AppointmentAvailabilityService $availabilityService
    ) {}

    public function createBooking(Workspace $workspace, array $data): BusinessAppointment
    {
        return DB::transaction(function () use ($workspace, $data) {
            $customer = $this->findOrCreateCustomer($workspace, $data);

            $service = null;

            if (! empty($data['service_id'])) {
                $service = BusinessService::query()
                    ->where('workspace_id', $workspace->id)
                    ->where('is_active', true)
                    ->find($data['service_id']);
            }

            $endTime = $this->availabilityService->calculateEndTime(
                startTime: $data['start_time'],
                service: $service
            );

            $this->availabilityService->ensureAvailable(
                workspace: $workspace,
                appointmentDate: $data['appointment_date'],
                startTime: $data['start_time'],
                endTime: $endTime
            );

            return $workspace->businessAppointments()->create([
                'customer_id' => $customer->id,
                'service_id' => $service?->id,

                'customer_name' => $customer->name,
                'customer_phone' => $customer->phone,
                'customer_email' => $customer->email,

                'appointment_date' => $data['appointment_date'],
                'start_time' => $data['start_time'],
                'end_time' => $endTime,

                'status' => 'pending',
                'source' => 'public_booking',
                'notes' => $data['notes'] ?? null,

                'metadata' => [
                    'created_from' => 'public_booking_form',
                ],
            ]);
        });
    }

    private function findOrCreateCustomer(Workspace $workspace, array $data): BusinessCustomer
    {
        $phone = trim($data['customer_phone']);

        $customer = $workspace->businessCustomers()
            ->where('phone', $phone)
            ->first();

        if ($customer) {
            $customer->update([
                'name' => $data['customer_name'],
                'email' => $data['customer_email'] ?? $customer->email,
                'status' => 'active',
            ]);

            return $customer;
        }

        return $workspace->businessCustomers()->create([
            'name' => $data['customer_name'],
            'phone' => $phone,
            'email' => $data['customer_email'] ?? null,
            'source' => 'public_booking',
            'status' => 'active',
            'notes' => null,
        ]);
    }
}