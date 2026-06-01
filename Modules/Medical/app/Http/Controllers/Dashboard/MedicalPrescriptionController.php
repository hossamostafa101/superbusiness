<?php

namespace Modules\Medical\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Medical\Models\MedicalPrescription;
use Modules\Medical\Models\MedicalVisit;

class MedicalPrescriptionController extends Controller
{
    public function index(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $prescriptions = $workspace->medicalPrescriptions()
            ->with([
                'patient:id,full_name,phone,patient_code',
                'staff:id,name',
                'visit:id,visit_number',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('prescription_number', 'like', "%{$search}%")
                        ->orWhere('patient_name', 'like', "%{$search}%")
                        ->orWhere('staff_name', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->latest('issued_at')
            ->paginate(20)
            ->withQueryString();

        return view('medical::dashboard.prescriptions.index', compact(
            'workspace',
            'prescriptions'
        ));
    }

    public function create(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $visit = null;

        if ($request->filled('visit_id')) {
            $visit = MedicalVisit::query()
                ->where('workspace_id', $workspace->id)
                ->with(['patient', 'staff'])
                ->findOrFail($request->input('visit_id'));
        }

        return view('medical::dashboard.prescriptions.create', compact(
            'workspace',
            'visit'
        ));
    }

    public function store(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $data = $this->validatedData($request, $workspace);

        $visit = MedicalVisit::query()
            ->where('workspace_id', $workspace->id)
            ->with(['patient', 'staff'])
            ->findOrFail($data['visit_id']);

        $prescription = DB::transaction(function () use ($workspace, $visit, $data) {
            $prescription = $workspace->medicalPrescriptions()->create([
                'visit_id' => $visit->id,
                'patient_id' => $visit->patient_id,
                'staff_id' => $visit->staff_id,

                'prescription_number' => $this->generatePrescriptionNumber($workspace),
                'issued_at' => $data['issued_at'] ?? now(),

                'diagnosis_summary' => $data['diagnosis_summary'] ?? $visit->diagnosis,
                'instructions' => $data['instructions'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'] ?? 'issued',

                'patient_name' => $visit->patient?->full_name ?: $visit->patient_name,
                'staff_name' => $visit->staff?->name ?: $visit->staff_name,
            ]);

            $this->syncItems($workspace, $prescription, $data['items'] ?? []);

            return $prescription;
        });

        return redirect()
            ->route('app.medical.prescriptions.show', [$workspace, $prescription])
            ->with('success', 'تم إنشاء الروشتة بنجاح.');
    }

    public function show(Workspace $workspace, MedicalPrescription $prescription)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $prescription);

        $prescription->load([
            'visit',
            'patient',
            'staff',
            'items',
        ]);

        return view('medical::dashboard.prescriptions.show', compact(
            'workspace',
            'prescription'
        ));
    }

    public function edit(Workspace $workspace, MedicalPrescription $prescription)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $prescription);

        $prescription->load(['visit', 'items']);

        return view('medical::dashboard.prescriptions.edit', compact(
            'workspace',
            'prescription'
        ));
    }

    public function update(Request $request, Workspace $workspace, MedicalPrescription $prescription)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $prescription);

        $data = $this->validatedData($request, $workspace, $prescription);

        DB::transaction(function () use ($workspace, $prescription, $data) {
            $prescription->update([
                'issued_at' => $data['issued_at'] ?? $prescription->issued_at,
                'diagnosis_summary' => $data['diagnosis_summary'] ?? null,
                'instructions' => $data['instructions'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'] ?? 'issued',
            ]);

            $prescription->items()->delete();

            $this->syncItems($workspace, $prescription, $data['items'] ?? []);
        });

        return redirect()
            ->route('app.medical.prescriptions.show', [$workspace, $prescription])
            ->with('success', 'تم تحديث الروشتة بنجاح.');
    }

    public function destroy(Workspace $workspace, MedicalPrescription $prescription)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $prescription);

        $prescription->delete();

        return redirect()
            ->route('app.medical.prescriptions.index', $workspace)
            ->with('success', 'تم حذف الروشتة.');
    }

    public function print(Workspace $workspace, MedicalPrescription $prescription)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $prescription);

        $prescription->load([
            'visit',
            'patient',
            'staff',
            'items',
        ]);

        $settings = $workspace->medicalSetting()->first();

        return view('medical::dashboard.prescriptions.print', compact(
            'workspace',
            'settings',
            'prescription'
        ));
    }

    private function validatedData(Request $request, Workspace $workspace, ?MedicalPrescription $prescription = null): array
    {
        return $request->validate([
            'visit_id' => [
                $prescription ? 'nullable' : 'required',
                Rule::exists('medical_visits', 'id')
                    ->where('workspace_id', $workspace->id),
            ],

            'issued_at' => ['nullable', 'date'],
            'diagnosis_summary' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'issued', 'cancelled'])],

            'items' => ['nullable', 'array'],
            'items.*.medicine_name' => ['nullable', 'string', 'max:190'],
            'items.*.dosage' => ['nullable', 'string', 'max:190'],
            'items.*.frequency' => ['nullable', 'string', 'max:190'],
            'items.*.duration' => ['nullable', 'string', 'max:190'],
            'items.*.route' => ['nullable', 'string', 'max:100'],
            'items.*.instructions' => ['nullable', 'string'],
        ]);
    }

    private function syncItems(Workspace $workspace, MedicalPrescription $prescription, array $items): void
    {
        foreach ($items as $index => $item) {
            $medicineName = trim((string) ($item['medicine_name'] ?? ''));

            if ($medicineName === '') {
                continue;
            }

            $prescription->items()->create([
                'workspace_id' => $workspace->id,
                'medicine_name' => $medicineName,
                'dosage' => $item['dosage'] ?? null,
                'frequency' => $item['frequency'] ?? null,
                'duration' => $item['duration'] ?? null,
                'route' => $item['route'] ?? null,
                'instructions' => $item['instructions'] ?? null,
                'sort_order' => $index,
            ]);
        }
    }

    private function generatePrescriptionNumber(Workspace $workspace): string
    {
        do {
            $number = 'RX' . now()->format('ymd') . random_int(1000, 9999);
        } while (
            MedicalPrescription::query()
                ->where('workspace_id', $workspace->id)
                ->where('prescription_number', $number)
                ->exists()
        );

        return $number;
    }

    private function ensureBelongsToWorkspace(Workspace $workspace, MedicalPrescription $prescription): void
    {
        abort_if((int) $prescription->workspace_id !== (int) $workspace->id, 404);
    }
}