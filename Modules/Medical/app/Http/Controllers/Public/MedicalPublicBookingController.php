<?php

namespace Modules\Medical\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Medical\Models\MedicalAppointment;
use Modules\Medical\Models\MedicalPatient;
use Modules\Medical\Models\MedicalService;
use Modules\Medical\Models\MedicalStaff;
use Modules\Medical\Models\MedicalStaffService;
use Modules\Medical\Models\MedicalStaffWorkingHour;

class MedicalPublicBookingController extends Controller
{
    public function create(Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $settings = $workspace->medicalSetting()->first();

        abort_if(! $settings || ! $settings->booking_enabled, 404);

        $branches = $workspace->medicalBranches()
            ->where('is_active', true)
            ->orderByDesc('is_main')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $services = $workspace->medicalServices()
            ->where('is_active', true)
            ->where('requires_appointment', true)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $staffMembers = $workspace->medicalStaff()
            ->where('is_active', true)
            ->where('accepts_online_booking', true)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('medical::public.booking.create', compact(
            'workspace',
            'settings',
            'branches',
            'services',
            'staffMembers'
        ));
    }

    public function availableSlots(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $settings = $workspace->medicalSetting()->first();

        abort_if(! $settings || ! $settings->booking_enabled, 404);

        $data = $request->validate([
            'staff_id' => [
                'required',
                Rule::exists('medical_staff', 'id')
                    ->where('workspace_id', $workspace->id),
            ],
            'service_id' => [
                'required',
                Rule::exists('medical_services', 'id')
                    ->where('workspace_id', $workspace->id),
            ],
            'branch_id' => [
                'nullable',
                Rule::exists('medical_branches', 'id')
                    ->where('workspace_id', $workspace->id),
            ],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $staff = MedicalStaff::query()
            ->where('workspace_id', $workspace->id)
            ->where('is_active', true)
            ->where('accepts_online_booking', true)
            ->findOrFail($data['staff_id']);

        $service = MedicalService::query()
            ->where('workspace_id', $workspace->id)
            ->where('is_active', true)
            ->where('requires_appointment', true)
            ->findOrFail($data['service_id']);

        $branchId = $data['branch_id'] ?? $staff->branch_id;

        $duration = $this->resolveDuration($service, $staff);

        $date = Carbon::parse($data['appointment_date']);
        $dayOfWeek = $date->dayOfWeek;

        $workingHours = MedicalStaffWorkingHour::query()
            ->where('workspace_id', $workspace->id)
            ->where('staff_id', $staff->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->where(function ($query) use ($branchId) {
                $query->whereNull('branch_id');

                if ($branchId) {
                    $query->orWhere('branch_id', $branchId);
                }
            })
            ->orderBy('starts_at')
            ->get();

        if ($workingHours->isEmpty()) {
            return response()->json([
                'slots' => [],
                'message' => 'لا توجد مواعيد متاحة في هذا اليوم.',
            ]);
        }

        $existingAppointments = MedicalAppointment::query()
            ->where('workspace_id', $workspace->id)
            ->where('staff_id', $staff->id)
            ->whereDate('appointment_date', $date->format('Y-m-d'))
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->get(['starts_at', 'ends_at']);

        $slots = [];

        foreach ($workingHours as $workingHour) {
            $slotMinutes = $workingHour->slot_minutes ?: $duration;

            $cursor = Carbon::parse($date->format('Y-m-d') . ' ' . $workingHour->starts_at);
            $end = Carbon::parse($date->format('Y-m-d') . ' ' . $workingHour->ends_at);

            while ($cursor->copy()->addMinutes($duration)->lte($end)) {
                $slotStart = $cursor->format('H:i');
                $slotEnd = $cursor->copy()->addMinutes($duration)->format('H:i');

                if ($date->isToday()) {
                    $slotDateTime = Carbon::parse($date->format('Y-m-d') . ' ' . $slotStart);

                    if ($slotDateTime->lessThan(now())) {
                        $cursor->addMinutes($slotMinutes);
                        continue;
                    }
                }

                $hasConflict = $existingAppointments->contains(function ($appointment) use ($slotStart, $slotEnd) {
                    $existingStart = Carbon::parse($appointment->starts_at)->format('H:i');
                    $existingEnd = Carbon::parse($appointment->ends_at)->format('H:i');

                    return $existingStart < $slotEnd && $existingEnd > $slotStart;
                });

                if (! $hasConflict) {
                    $slots[] = [
                        'starts_at' => $slotStart,
                        'ends_at' => $slotEnd,
                        'label' => $slotStart . ' - ' . $slotEnd,
                    ];
                }

                $cursor->addMinutes($slotMinutes);
            }
        }

        return response()->json([
            'slots' => $slots,
            'duration' => $duration,
        ]);
    }

    public function store(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $settings = $workspace->medicalSetting()->first();

        abort_if(! $settings || ! $settings->booking_enabled, 404);

        $data = $request->validate([
            'branch_id' => [
                'nullable',
                Rule::exists('medical_branches', 'id')
                    ->where('workspace_id', $workspace->id),
            ],
            'service_id' => [
                'required',
                Rule::exists('medical_services', 'id')
                    ->where('workspace_id', $workspace->id),
            ],
            'staff_id' => [
                'required',
                Rule::exists('medical_staff', 'id')
                    ->where('workspace_id', $workspace->id),
            ],

            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'starts_at' => ['required', 'date_format:H:i'],
            'ends_at' => ['required', 'date_format:H:i', 'after:starts_at'],

            'patient_name' => ['required', 'string', 'max:190'],
            'patient_phone' => ['required', 'string', 'max:50'],
            'patient_email' => ['nullable', 'email', 'max:190'],

            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $service = MedicalService::query()
            ->where('workspace_id', $workspace->id)
            ->where('is_active', true)
            ->where('requires_appointment', true)
            ->findOrFail($data['service_id']);

        $staff = MedicalStaff::query()
            ->where('workspace_id', $workspace->id)
            ->where('is_active', true)
            ->where('accepts_online_booking', true)
            ->findOrFail($data['staff_id']);



            $hasServiceLink = MedicalStaffService::query()
    ->where('workspace_id', $workspace->id)
    ->where('staff_id', $staff->id)
    ->where('service_id', $service->id)
    ->where('is_active', true)
    ->exists();

if (! $hasServiceLink) {
    return back()
        ->withInput()
        ->withErrors([
            'staff_id' => 'المختص المحدد لا يقدم هذه الخدمة.',
        ]);
}




        $branchId = $data['branch_id'] ?? $staff->branch_id;

        if (! $this->isWithinWorkingHours(
            workspace: $workspace,
            staffId: $staff->id,
            branchId: $branchId,
            appointmentDate: $data['appointment_date'],
            startsAt: $data['starts_at'],
            endsAt: $data['ends_at']
        )) {
            return back()
                ->withInput()
                ->withErrors([
                    'starts_at' => 'هذا الموعد غير متاح.',
                ]);
        }

        if ($this->hasAppointmentConflict(
            workspace: $workspace,
            staffId: $staff->id,
            appointmentDate: $data['appointment_date'],
            startsAt: $data['starts_at'],
            endsAt: $data['ends_at']
        )) {
            return back()
                ->withInput()
                ->withErrors([
                    'starts_at' => 'هذا الموعد تم حجزه بالفعل.',
                ]);
        }

        $patient = $this->findOrCreatePatient(
            workspace: $workspace,
            name: $data['patient_name'],
            phone: $data['patient_phone'],
            email: $data['patient_email'] ?? null
        );

        $price = $this->resolvePrice($service, $staff);

        $appointment = $workspace->medicalAppointments()->create([
            'branch_id' => $branchId,
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'staff_id' => $staff->id,

            'appointment_number' => $this->generateAppointmentNumber($workspace),

            'appointment_date' => $data['appointment_date'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],

            'status' => 'pending',
            'payment_status' => 'unpaid',
            'source' => 'public_booking',

            'patient_name' => $patient->full_name,
            'patient_phone' => $patient->phone ?: $patient->whatsapp_number,
            'patient_email' => $patient->email,

            'service_name' => $service->name,
            'staff_name' => $staff->name,

            'price' => $price,
            'currency' => $service->currency ?: $settings->default_currency ?: 'EGP',

            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('public.medical.booking.success', [$workspace, $appointment]);
    }

    public function success(Workspace $workspace, MedicalAppointment $appointment)
    {
        abort_if($workspace->type !== 'medical', 404);
        abort_if((int) $appointment->workspace_id !== (int) $workspace->id, 404);

        return view('medical::public.booking.success', compact(
            'workspace',
            'appointment'
        ));
    }

    private function findOrCreatePatient(Workspace $workspace, string $name, string $phone, ?string $email): MedicalPatient
    {
        $patient = MedicalPatient::query()
            ->where('workspace_id', $workspace->id)
            ->where(function ($query) use ($phone, $email) {
                $query->where('phone', $phone)
                    ->orWhere('whatsapp_number', $phone);

                if ($email) {
                    $query->orWhere('email', $email);
                }
            })
            ->first();

        if ($patient) {
            return $patient;
        }

        return $workspace->medicalPatients()->create([
            'patient_code' => $this->generatePatientCode($workspace),
            'full_name' => $name,
            'phone' => $phone,
            'email' => $email,
            'gender' => 'unknown',
            'status' => 'active',
        ]);
    }

    private function resolvePrice(MedicalService $service, ?MedicalStaff $staff): ?float
    {
        if (! $staff) {
            return $service->price !== null ? (float) $service->price : null;
        }

        $staffService = MedicalStaffService::query()
            ->where('staff_id', $staff->id)
            ->where('service_id', $service->id)
            ->first();

        if ($staffService?->price_override !== null) {
            return (float) $staffService->price_override;
        }

        return $service->price !== null ? (float) $service->price : null;
    }

    private function resolveDuration(MedicalService $service, ?MedicalStaff $staff): int
    {
        if ($staff) {
            $staffService = MedicalStaffService::query()
                ->where('staff_id', $staff->id)
                ->where('service_id', $service->id)
                ->first();

            if ($staffService?->duration_override) {
                return (int) $staffService->duration_override;
            }

            if ($staff->default_slot_minutes) {
                return (int) $staff->default_slot_minutes;
            }
        }

        return (int) ($service->duration_minutes ?: 30);
    }

    private function hasAppointmentConflict(
        Workspace $workspace,
        ?int $staffId,
        string $appointmentDate,
        string $startsAt,
        string $endsAt
    ): bool {
        if (! $staffId) {
            return false;
        }

        return MedicalAppointment::query()
            ->where('workspace_id', $workspace->id)
            ->where('staff_id', $staffId)
            ->whereDate('appointment_date', $appointmentDate)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->where(function ($query) use ($startsAt, $endsAt) {
                $query->where('starts_at', '<', $endsAt)
                    ->where('ends_at', '>', $startsAt);
            })
            ->exists();
    }

    private function isWithinWorkingHours(
        Workspace $workspace,
        ?int $staffId,
        ?int $branchId,
        string $appointmentDate,
        string $startsAt,
        string $endsAt
    ): bool {
        if (! $staffId) {
            return true;
        }

        $dayOfWeek = Carbon::parse($appointmentDate)->dayOfWeek;

        return MedicalStaffWorkingHour::query()
            ->where('workspace_id', $workspace->id)
            ->where('staff_id', $staffId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->where(function ($query) use ($branchId) {
                $query->whereNull('branch_id');

                if ($branchId) {
                    $query->orWhere('branch_id', $branchId);
                }
            })
            ->where('starts_at', '<=', $startsAt)
            ->where('ends_at', '>=', $endsAt)
            ->exists();
    }

    private function generateAppointmentNumber(Workspace $workspace): string
    {
        do {
            $number = 'A' . now()->format('ymd') . random_int(1000, 9999);
        } while (
            MedicalAppointment::query()
                ->where('workspace_id', $workspace->id)
                ->where('appointment_number', $number)
                ->exists()
        );

        return $number;
    }

    private function generatePatientCode(Workspace $workspace): string
    {
        do {
            $code = 'P' . now()->format('ymd') . random_int(1000, 9999);
        } while (
            MedicalPatient::query()
                ->where('workspace_id', $workspace->id)
                ->where('patient_code', $code)
                ->exists()
        );

        return $code;
    }


    public function staffByService(Request $request, Workspace $workspace)
{
    abort_if($workspace->type !== 'medical', 404);

    $settings = $workspace->medicalSetting()->first();

    abort_if(! $settings || ! $settings->booking_enabled, 404);

    $data = $request->validate([
        'service_id' => [
            'required',
            Rule::exists('medical_services', 'id')
                ->where('workspace_id', $workspace->id),
        ],
        'branch_id' => [
            'nullable',
            Rule::exists('medical_branches', 'id')
                ->where('workspace_id', $workspace->id),
        ],
    ]);

    $service = MedicalService::query()
        ->where('workspace_id', $workspace->id)
        ->where('is_active', true)
        ->where('requires_appointment', true)
        ->findOrFail($data['service_id']);

    $branchId = $data['branch_id'] ?? null;

    $staffQuery = MedicalStaff::query()
        ->where('workspace_id', $workspace->id)
        ->where('is_active', true)
        ->where('accepts_online_booking', true)
        ->where(function ($query) use ($branchId) {
            if ($branchId) {
                $query->whereNull('branch_id')
                    ->orWhere('branch_id', $branchId);
            }
        })
        ->whereHas('staffServices', function ($query) use ($service) {
            $query->where('service_id', $service->id)
                ->where('is_active', true);
        })
        ->with([
            'staffServices' => function ($query) use ($service) {
                $query->where('service_id', $service->id);
            },
            'specialty:id,name',
            'department:id,name',
        ])
        ->orderByDesc('is_featured')
        ->orderBy('sort_order')
        ->orderBy('name');

    $staffMembers = $staffQuery->get();

    /*
     * fallback:
     * لو المستخدم لم يربط الخدمة بأي طبيب بعد،
     * نعرض أعضاء الفريق المتاحين للحجز حتى لا تصبح الصفحة فارغة.
     */
    if ($staffMembers->isEmpty()) {
        $staffMembers = MedicalStaff::query()
            ->where('workspace_id', $workspace->id)
            ->where('is_active', true)
            ->where('accepts_online_booking', true)
            ->where(function ($query) use ($branchId) {
                if ($branchId) {
                    $query->whereNull('branch_id')
                        ->orWhere('branch_id', $branchId);
                }
            })
            ->with(['specialty:id,name', 'department:id,name'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    return response()->json([
        'staff' => $staffMembers->map(function (MedicalStaff $staff) use ($service) {
            $staffService = $staff->staffServices
                ? $staff->staffServices->first()
                : null;

            $price = $staffService?->price_override !== null
                ? (float) $staffService->price_override
                : ($service->price !== null ? (float) $service->price : null);

            $duration = $staffService?->duration_override
                ?: $staff->default_slot_minutes
                ?: $service->duration_minutes
                ?: 30;

            return [
                'id' => $staff->id,
                'name' => $staff->name,
                'title' => $staff->title,
                'specialty' => $staff->specialty?->name,
                'department' => $staff->department?->name,
                'price' => $price,
                'currency' => $service->currency ?: 'EGP',
                'duration' => (int) $duration,
                'is_featured' => (bool) $staff->is_featured,
            ];
        })->values(),
    ]);
}
}