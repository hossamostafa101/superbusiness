<?php

namespace Modules\Medical\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Medical\Models\MedicalAppointment;
use Modules\Medical\Models\MedicalPatient;
use Modules\Medical\Models\MedicalService;
use Modules\Medical\Models\MedicalStaff;
use Modules\Medical\Models\MedicalVisit;

class MedicalVisitController extends Controller
{
    public function index(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $visits = $workspace->medicalVisits()
            ->with([
                'patient:id,full_name,phone,patient_code',
                'staff:id,name',
                'service:id,name',
                'branch:id,name',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('visit_number', 'like', "%{$search}%")
                        ->orWhere('patient_name', 'like', "%{$search}%")
                        ->orWhere('staff_name', 'like', "%{$search}%")
                        ->orWhere('service_name', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $query->whereDate('visit_date', $request->input('date'));
            })
            ->when(! $request->filled('date'), function ($query) {
                $query->whereDate('visit_date', today());
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->latest('visit_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('medical::dashboard.visits.index', compact(
            'workspace',
            'visits'
        ));
    }

    public function create(Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        return view('medical::dashboard.visits.create', $this->formPayload($workspace));
    }

    public function store(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $data = $this->validatedData($request, $workspace);

        $patient = MedicalPatient::query()
            ->where('workspace_id', $workspace->id)
            ->findOrFail($data['patient_id']);

        $staff = ! empty($data['staff_id'])
            ? MedicalStaff::query()->where('workspace_id', $workspace->id)->find($data['staff_id'])
            : null;

        $service = ! empty($data['service_id'])
            ? MedicalService::query()->where('workspace_id', $workspace->id)->find($data['service_id'])
            : null;

        $visit = $workspace->medicalVisits()->create([
            'branch_id' => $data['branch_id'] ?? $staff?->branch_id,
            'patient_id' => $patient->id,
            'staff_id' => $staff?->id,
            'service_id' => $service?->id,

            'visit_number' => $this->generateVisitNumber($workspace),
            'visit_date' => $data['visit_date'],
            'started_at' => $data['started_at'] ?? now(),

            'status' => $data['status'] ?? 'open',
            'visit_type' => $data['visit_type'] ?? 'consultation',

            'chief_complaint' => $data['chief_complaint'] ?? null,
            'diagnosis' => $data['diagnosis'] ?? null,
            'treatment_plan' => $data['treatment_plan'] ?? null,
            'notes' => $data['notes'] ?? null,
            'internal_notes' => $data['internal_notes'] ?? null,

            'patient_name' => $patient->full_name,
            'staff_name' => $staff?->name,
            'service_name' => $service?->name,
        ]);

        return redirect()
            ->route('app.medical.visits.show', [$workspace, $visit])
            ->with('success', 'تم إنشاء الزيارة بنجاح.');
    }

    public function show(Workspace $workspace, MedicalVisit $visit)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $visit);

        $visit->load([
            'branch',
            'appointment',
            'patient',
            'staff',
            'service',
            'visitNotes.staff',
            'prescriptions.items',
        ]);

        return view('medical::dashboard.visits.show', compact(
            'workspace',
            'visit'
        ));
    }

    public function edit(Workspace $workspace, MedicalVisit $visit)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $visit);

        return view('medical::dashboard.visits.edit', array_merge(
            $this->formPayload($workspace),
            compact('visit')
        ));
    }

    public function update(Request $request, Workspace $workspace, MedicalVisit $visit)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $visit);

        $data = $this->validatedData($request, $workspace, $visit);

        $patient = MedicalPatient::query()
            ->where('workspace_id', $workspace->id)
            ->findOrFail($data['patient_id']);

        $staff = ! empty($data['staff_id'])
            ? MedicalStaff::query()->where('workspace_id', $workspace->id)->find($data['staff_id'])
            : null;

        $service = ! empty($data['service_id'])
            ? MedicalService::query()->where('workspace_id', $workspace->id)->find($data['service_id'])
            : null;

        $payload = [
            'branch_id' => $data['branch_id'] ?? $staff?->branch_id,
            'patient_id' => $patient->id,
            'staff_id' => $staff?->id,
            'service_id' => $service?->id,

            'visit_date' => $data['visit_date'],
            'started_at' => $data['started_at'] ?? $visit->started_at,
            'ended_at' => $data['ended_at'] ?? $visit->ended_at,

            'status' => $data['status'],
            'visit_type' => $data['visit_type'],

            'chief_complaint' => $data['chief_complaint'] ?? null,
            'diagnosis' => $data['diagnosis'] ?? null,
            'treatment_plan' => $data['treatment_plan'] ?? null,
            'notes' => $data['notes'] ?? null,
            'internal_notes' => $data['internal_notes'] ?? null,

            'patient_name' => $patient->full_name,
            'staff_name' => $staff?->name,
            'service_name' => $service?->name,
        ];

        if ($data['status'] === 'completed' && ! $visit->ended_at) {
            $payload['ended_at'] = now();
        }

        $visit->update($payload);

        return redirect()
            ->route('app.medical.visits.show', [$workspace, $visit])
            ->with('success', 'تم تحديث الزيارة بنجاح.');
    }

    public function destroy(Workspace $workspace, MedicalVisit $visit)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $visit);

        $visit->delete();

        return redirect()
            ->route('app.medical.visits.index', $workspace)
            ->with('success', 'تم حذف الزيارة.');
    }

    public function startFromAppointment(Workspace $workspace, MedicalAppointment $appointment)
    {
        abort_if($workspace->type !== 'medical', 404);
        abort_if((int) $appointment->workspace_id !== (int) $workspace->id, 404);

        if ($appointment->visit) {
            return redirect()
                ->route('app.medical.visits.show', [$workspace, $appointment->visit])
                ->with('success', 'هذه الزيارة موجودة بالفعل.');
        }

        $visit = $workspace->medicalVisits()->create([
            'branch_id' => $appointment->branch_id,
            'appointment_id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'staff_id' => $appointment->staff_id,
            'service_id' => $appointment->service_id,

            'visit_number' => $this->generateVisitNumber($workspace),
            'visit_date' => today(),
            'started_at' => now(),

            'status' => 'in_progress',
            'visit_type' => $this->mapVisitTypeFromAppointment($appointment),

            'patient_name' => $appointment->patient_name,
            'staff_name' => $appointment->staff_name,
            'service_name' => $appointment->service_name,
        ]);

        $appointment->update([
            'status' => 'in_progress',
        ]);

        return redirect()
            ->route('app.medical.visits.show', [$workspace, $visit])
            ->with('success', 'تم بدء الزيارة.');
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
            ->latest('id')
            ->limit(300)
            ->get(['id', 'full_name', 'phone', 'patient_code']);

        $staffMembers = $workspace->medicalStaff()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'branch_id']);

        $services = $workspace->medicalServices()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'type']);

        return compact(
            'workspace',
            'branches',
            'patients',
            'staffMembers',
            'services'
        );
    }

    private function validatedData(Request $request, Workspace $workspace, ?MedicalVisit $visit = null): array
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
            'staff_id' => [
                'nullable',
                Rule::exists('medical_staff', 'id')
                    ->where('workspace_id', $workspace->id),
            ],
            'service_id' => [
                'nullable',
                Rule::exists('medical_services', 'id')
                    ->where('workspace_id', $workspace->id),
            ],

            'visit_date' => ['required', 'date'],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],

            'status' => ['required', Rule::in(['open', 'in_progress', 'completed', 'cancelled'])],
            'visit_type' => ['required', Rule::in(['consultation', 'follow_up', 'procedure', 'lab', 'scan', 'emergency', 'other'])],

            'chief_complaint' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'treatment_plan' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ]);
    }

    private function mapVisitTypeFromAppointment(MedicalAppointment $appointment): string
    {
        return match ($appointment->service?->type) {
            'follow_up' => 'follow_up',
            'procedure' => 'procedure',
            'lab_test' => 'lab',
            'scan' => 'scan',
            default => 'consultation',
        };
    }

    private function generateVisitNumber(Workspace $workspace): string
    {
        do {
            $number = 'V' . now()->format('ymd') . random_int(1000, 9999);
        } while (
            MedicalVisit::query()
                ->where('workspace_id', $workspace->id)
                ->where('visit_number', $number)
                ->exists()
        );

        return $number;
    }

    private function ensureBelongsToWorkspace(Workspace $workspace, MedicalVisit $visit): void
    {
        abort_if((int) $visit->workspace_id !== (int) $workspace->id, 404);
    }
}