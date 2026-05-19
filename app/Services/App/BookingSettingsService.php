<?php

namespace App\Services\App;

use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class BookingSettingsService
{
    public function update(Workspace $workspace, array $data): void
    {
        DB::transaction(function () use ($workspace, $data) {
            $settings = [
                'booking_enabled' => ! empty($data['booking_enabled']) ? '1' : '0',
                'booking_days' => implode(',', $data['booking_days'] ?? []),
                'booking_start_time' => $data['booking_start_time'] ?? '10:00',
                'booking_end_time' => $data['booking_end_time'] ?? '22:00',
                'booking_slot_interval' => (string) ($data['booking_slot_interval'] ?? 30),
                'booking_advance_days' => (string) ($data['booking_advance_days'] ?? 14),
                'booking_buffer_minutes' => (string) ($data['booking_buffer_minutes'] ?? 0),
            ];

            foreach ($settings as $key => $value) {
                $workspace->businessSettings()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        });
    }

    public function defaults(Workspace $workspace): array
    {
        $workspace->loadMissing('businessSettings');

        return [
            'booking_enabled' => $workspace->getSetting('booking_enabled', '1'),
            'booking_days' => explode(',', $workspace->getSetting('booking_days', 'sat,sun,mon,tue,wed,thu')),
            'booking_start_time' => $workspace->getSetting('booking_start_time', '10:00'),
            'booking_end_time' => $workspace->getSetting('booking_end_time', '22:00'),
            'booking_slot_interval' => (int) $workspace->getSetting('booking_slot_interval', 30),
            'booking_advance_days' => (int) $workspace->getSetting('booking_advance_days', 14),
            'booking_buffer_minutes' => (int) $workspace->getSetting('booking_buffer_minutes', 0),
        ];
    }
}