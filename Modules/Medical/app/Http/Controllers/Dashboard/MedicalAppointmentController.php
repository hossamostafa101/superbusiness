<?php

namespace Modules\Medical\Http\Controllers\Dashboard;

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

class MedicalAppointmentController extends Controller
{
    public function index(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $appointments = $workspace->medicalAppointments()
            ->with([
                'branch:id,name',
                'patient:id,full_name,phone,patient_code',
                'service:id,name',
                'staff:id,name',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('appointment_number', 'like', "%{$search}%")
                        ->orWhere('patient_name', 'like', "%{$search}%")
                        ->orWhere('patient_phone', 'like', "%{$search}%")
                        ->orWhere('service_name', 'like', "%{$search}%")
                        ->orWhere('staff_name', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $query->whereDate('appointment_date', $request->input('date'));
            })
            ->when(! $request->filled('date'), function ($query) {
                $query->whereDate('appointment_date', today());
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('staff_id'), function ($query) use ($request) {
                $query->where('staff_id', $request->input('staff_id'));
            })
            ->orderBy('appointment_date')
            ->orderBy('starts_at')
            ->paginate(20)
            ->withQueryString();

        $staffMembers = $workspace->medicalStaff()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('medical::dashboard.appointments.index', compact(
            'workspace',
            'appointments',
            'staffMembers'
        ));
    }

    public function create(Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        return view('medical::dashboard.appointments.create', $this->formPayload($workspace));
    }

    public function store(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $data = $this->validatedData($request, $workspace);

        $patient = MedicalPatient::query()
            ->where('workspace_id', $workspace->id)
            ->findOrFail($data['patient_id']);

        $service = MedicalService::query()
            ->where('workspace_id', $workspace->id)
            ->findOrFail($data['service_id']);

        $staff = null;

        if (! empty($data['staff_id'])) {
            $staff = MedicalStaff::query()
                ->where('workspace_id', $workspace->id)
                ->findOrFail($data['staff_id']);
        }

        $duration = $this->resolveDuration($service, $staff);
        $startsAt = Carbon::parse($data['starts_at']);
        $endsAt = $data['ends_at'] ?? $startsAt->copy()->addMinutes($duration)->format('H:i');

        $price = $this->resolvePrice($service, $staff);


        




        if ($this->hasAppointmentConflict(
    workspace: $workspace,
    staffId: $staff?->id,
    appointmentDate: $data['appointment_date'],
    startsAt: $data['starts_at'],
    endsAt: $endsAt
)) {
    return back()
        ->withInput()
        ->withErrors([
            'starts_at' => 'يوجد حجز آخر لنفس عضو الفريق في هذا الوقت.',
        ]);
}



$branchId = $data['branch_id'] ?? $staff?->branch_id;

if (! $this->isWithinWorkingHours(
    workspace: $workspace,
    staffId: $staff?->id,
    branchId: $branchId,
    appointmentDate: $data['appointment_date'],
    startsAt: $data['starts_at'],
    endsAt: $endsAt
)) {
    return back()
        ->withInput()
        ->withErrors([
            'starts_at' => 'هذا الموعد خارج مواعيد عمل عضو الفريق.',
        ]);
}




        $appointment = $workspace->medicalAppointments()->create([
            // 'branch_id' => $data['branch_id'] ?? $staff?->branch_id,
            'branch_id' => $branchId,
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'staff_id' => $staff?->id,

            'appointment_number' => $this->generateAppointmentNumber($workspace),

            'appointment_date' => $data['appointment_date'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $endsAt,

            'status' => $data['status'] ?? 'confirmed',
            'payment_status' => $data['payment_status'] ?? 'unpaid',
            'source' => 'dashboard',

            'patient_name' => $patient->full_name,
            'patient_phone' => $patient->phone ?: $patient->whatsapp_number,
            'patient_email' => $patient->email,

            'service_name' => $service->name,
            'staff_name' => $staff?->name,

            'price' => $price,
            'currency' => $service->currency ?: 'EGP',

            'notes' => $data['notes'] ?? null,
            'internal_notes' => $data['internal_notes'] ?? null,
        ]);

        return redirect()
            ->route('app.medical.appointments.show', [$workspace, $appointment])
            ->with('success', 'تم إنشاء الحجز بنجاح.');
    }

    public function show(Workspace $workspace, MedicalAppointment $appointment)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $appointment);

        $appointment->load([
    'branch',
    'patient',
    'service',
    'staff',
    'visit',
]);

        return view('medical::dashboard.appointments.show', compact(
            'workspace',
            'appointment'
        ));
    }

    public function edit(Workspace $workspace, MedicalAppointment $appointment)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $appointment);

        return view('medical::dashboard.appointments.edit', array_merge(
            $this->formPayload($workspace),
            compact('appointment')
        ));
    }

    public function update(Request $request, Workspace $workspace, MedicalAppointment $appointment)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $appointment);

        $data = $this->validatedData($request, $workspace, $appointment);

        $patient = MedicalPatient::query()
            ->where('workspace_id', $workspace->id)
            ->findOrFail($data['patient_id']);

        $service = MedicalService::query()
            ->where('workspace_id', $workspace->id)
            ->findOrFail($data['service_id']);

        $staff = null;

        if (! empty($data['staff_id'])) {
            $staff = MedicalStaff::query()
                ->where('workspace_id', $workspace->id)
                ->findOrFail($data['staff_id']);
        }

        $duration = $this->resolveDuration($service, $staff);
        $startsAt = Carbon::parse($data['starts_at']);
        $endsAt = $data['ends_at'] ?? $startsAt->copy()->addMinutes($duration)->format('H:i');






        if ($this->hasAppointmentConflict(
    workspace: $workspace,
    staffId: $staff?->id,
    appointmentDate: $data['appointment_date'],
    startsAt: $data['starts_at'],
    endsAt: $endsAt,
    ignoreAppointmentId: $appointment->id
)) {
    return back()
        ->withInput()
        ->withErrors([
            'starts_at' => 'يوجد حجز آخر لنفس عضو الفريق في هذا الوقت.',
        ]);
}



$branchId = $data['branch_id'] ?? $staff?->branch_id;

if (! $this->isWithinWorkingHours(
    workspace: $workspace,
    staffId: $staff?->id,
    branchId: $branchId,
    appointmentDate: $data['appointment_date'],
    startsAt: $data['starts_at'],
    endsAt: $endsAt
)) {
    return back()
        ->withInput()
        ->withErrors([
            'starts_at' => 'هذا الموعد خارج مواعيد عمل عضو الفريق.',
        ]);
}









        $appointment->update([
            'branch_id' => $branchId,
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'staff_id' => $staff?->id,

            'appointment_date' => $data['appointment_date'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $endsAt,

            'status' => $data['status'] ?? $appointment->status,
            'payment_status' => $data['payment_status'] ?? $appointment->payment_status,

            'patient_name' => $patient->full_name,
            'patient_phone' => $patient->phone ?: $patient->whatsapp_number,
            'patient_email' => $patient->email,

            'service_name' => $service->name,
            'staff_name' => $staff?->name,

            'price' => $this->resolvePrice($service, $staff),
            'currency' => $service->currency ?: 'EGP',

            'notes' => $data['notes'] ?? null,
            'internal_notes' => $data['internal_notes'] ?? null,
        ]);

        return redirect()
            ->route('app.medical.appointments.show', [$workspace, $appointment])
            ->with('success', 'تم تحديث الحجز بنجاح.');
    }

    public function destroy(Workspace $workspace, MedicalAppointment $appointment)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $appointment);

        $appointment->delete();

        return redirect()
            ->route('app.medical.appointments.index', $workspace)
            ->with('success', 'تم حذف الحجز.');
    }

    public function updateStatus(Request $request, Workspace $workspace, MedicalAppointment $appointment)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $appointment);

        $data = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'confirmed',
                    'checked_in',
                    'in_progress',
                    'completed',
                    'cancelled',
                    'no_show',
                ]),
            ],
        ]);

        $payload = [
            'status' => $data['status'],
        ];

        if ($data['status'] === 'cancelled') {
            $payload['cancelled_at'] = now();
        }

        $appointment->update($payload);

        return back()->with('success', 'تم تحديث حالة الحجز.');
    }

    private function formPayload(Workspace $workspace): array
    {
        $branches = $workspace->medicalBranches()
            ->where('is_active', true)
            ->orderByDesc('is_main')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        $patients = $workspace->medicalPatients()
            ->where('status', 'active')
            ->orderByDesc('id')
            ->limit(200)
            ->get(['id', 'full_name', 'phone', 'patient_code']);

        $services = $workspace->medicalServices()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'currency', 'duration_minutes']);

        $staffMembers = $workspace->medicalStaff()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'branch_id', 'default_slot_minutes']);

        return compact(
            'workspace',
            'branches',
            'patients',
            'services',
            'staffMembers'
        );
    }

    private function validatedData(Request $request, Workspace $workspace, ?MedicalAppointment $appointment = null): array
    {
        return $request->validate([
            'branch_id' => [
                'nullable',
                Rule::exists('medical_branches', 'id')
                    ->where('workspace_id', $workspace->id),
            ],
            'patient_id' => [
                'required',
                Rule::exists('medical_patients', 'id')
                    ->where('workspace_id', $workspace->id),
            ],
            'service_id' => [
                'required',
                Rule::exists('medical_services', 'id')
                    ->where('workspace_id', $workspace->id),
            ],
            'staff_id' => [
                'nullable',
                Rule::exists('medical_staff', 'id')
                    ->where('workspace_id', $workspace->id),
            ],

            'appointment_date' => ['required', 'date'],
            'starts_at' => ['required', 'date_format:H:i'],
            'ends_at' => ['nullable', 'date_format:H:i', 'after:starts_at'],

            'status' => [
                'nullable',
                Rule::in([
                    'pending',
                    'confirmed',
                    'checked_in',
                    'in_progress',
                    'completed',
                    'cancelled',
                    'no_show',
                ]),
            ],

            'payment_status' => [
                'nullable',
                Rule::in([
                    'unpaid',
                    'partially_paid',
                    'paid',
                    'refunded',
                ]),
            ],

            'notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
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

    private function ensureBelongsToWorkspace(Workspace $workspace, MedicalAppointment $appointment): void
    {
        abort_if((int) $appointment->workspace_id !== (int) $workspace->id, 404);
    }







    private function hasAppointmentConflict(
    Workspace $workspace,
    ?int $staffId,
    string $appointmentDate,
    string $startsAt,
    string $endsAt,
    ?int $ignoreAppointmentId = null
): bool {
    if (! $staffId) {
        return false;
    }

    return MedicalAppointment::query()
        ->where('workspace_id', $workspace->id)
        ->where('staff_id', $staffId)
        ->whereDate('appointment_date', $appointmentDate)
        ->whereNotIn('status', ['cancelled', 'no_show'])
        ->when($ignoreAppointmentId, function ($query) use ($ignoreAppointmentId) {
            $query->whereKeyNot($ignoreAppointmentId);
        })
        ->where(function ($query) use ($startsAt, $endsAt) {
            $query
                ->where(function ($q) use ($startsAt, $endsAt) {
                    $q->where('starts_at', '<', $endsAt)
                        ->where('ends_at', '>', $startsAt);
                });
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

    $dayOfWeek = \Carbon\Carbon::parse($appointmentDate)->dayOfWeek;

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






public function availableSlots(Request $request, Workspace $workspace)
{
    abort_if($workspace->type !== 'medical', 404);

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
        'appointment_date' => ['required', 'date'],
        'ignore_appointment_id' => ['nullable', 'integer'],
    ]);

    $staff = MedicalStaff::query()
        ->where('workspace_id', $workspace->id)
        ->findOrFail($data['staff_id']);

    $service = MedicalService::query()
        ->where('workspace_id', $workspace->id)
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
            'message' => 'لا توجد مواعيد عمل في هذا اليوم.',
        ]);
    }

    $existingAppointments = MedicalAppointment::query()
        ->where('workspace_id', $workspace->id)
        ->where('staff_id', $staff->id)
        ->whereDate('appointment_date', $date->format('Y-m-d'))
        ->whereNotIn('status', ['cancelled', 'no_show'])
        ->when($request->filled('ignore_appointment_id'), function ($query) use ($request) {
            $query->whereKeyNot((int) $request->input('ignore_appointment_id'));
        })
        ->get(['starts_at', 'ends_at']);

    $slots = [];

    foreach ($workingHours as $workingHour) {
        $slotMinutes = $workingHour->slot_minutes ?: $duration;

        $cursor = Carbon::parse($date->format('Y-m-d') . ' ' . $workingHour->starts_at);
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $workingHour->ends_at);

        while ($cursor->copy()->addMinutes($duration)->lte($end)) {
            $slotStart = $cursor->format('H:i');
            $slotEnd = $cursor->copy()->addMinutes($duration)->format('H:i');

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
}