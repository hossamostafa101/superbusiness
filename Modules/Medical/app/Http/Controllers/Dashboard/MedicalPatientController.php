<?php

namespace Modules\Medical\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Medical\Models\MedicalPatient;

class MedicalPatientController extends Controller
{
    public function index(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $patients = $workspace->medicalPatients()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                        ->orWhere('patient_code', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('whatsapp_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('national_id', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('gender'), function ($query) use ($request) {
                $query->where('gender', $request->input('gender'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('medical::dashboard.patients.index', compact(
            'workspace',
            'patients'
        ));
    }

    public function create(Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        return view('medical::dashboard.patients.create', compact('workspace'));
    }

    public function store(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $data = $this->validatedData($request, $workspace);

        $data['workspace_id'] = $workspace->id;
        $data['full_name'] = $this->makeFullName($data);
        $data['patient_code'] = $data['patient_code'] ?: $this->generatePatientCode($workspace);

        $workspace->medicalPatients()->create($data);

        return redirect()
            ->route('app.medical.patients.index', $workspace)
            ->with('success', 'تم إضافة المريض بنجاح.');
    }

    public function show(Workspace $workspace, MedicalPatient $patient)
{
    abort_if($workspace->type !== 'medical', 404);
    $this->ensureBelongsToWorkspace($workspace, $patient);

    $patient->loadCount([
        'appointments',
        'visits',
    ]);

    $upcomingAppointments = $patient->appointments()
        ->with([
            'branch:id,name',
            'service:id,name',
            'staff:id,name',
        ])
        ->whereDate('appointment_date', '>=', today())
        ->whereNotIn('status', ['completed', 'cancelled', 'no_show'])
        ->orderBy('appointment_date')
        ->orderBy('starts_at')
        ->limit(8)
        ->get();

    $latestVisits = $patient->visits()
        ->with([
            'branch:id,name',
            'service:id,name',
            'staff:id,name',
            'appointment:id,appointment_number',
        ])
        ->latest('visit_date')
        ->latest('id')
        ->limit(10)
        ->get();

    $previousAppointments = $patient->appointments()
        ->with([
            'branch:id,name',
            'service:id,name',
            'staff:id,name',
        ])
        ->where(function ($query) {
            $query->whereDate('appointment_date', '<', today())
                ->orWhereIn('status', ['completed', 'cancelled', 'no_show']);
        })
        ->latest('appointment_date')
        ->latest('starts_at')
        ->limit(10)
        ->get();

    $stats = [
        'appointments_total' => $patient->appointments_count,
        'visits_total' => $patient->visits_count,
        'upcoming' => $upcomingAppointments->count(),
        'completed_visits' => $patient->visits()
            ->where('status', 'completed')
            ->count(),
    ];

    return view('medical::dashboard.patients.show', compact(
        'workspace',
        'patient',
        'upcomingAppointments',
        'latestVisits',
        'previousAppointments',
        'stats'
    ));
}
    public function showX(Workspace $workspace, MedicalPatient $patient)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $patient);

        return view('medical::dashboard.patients.show', compact(
            'workspace',
            'patient'
        ));
    }

    public function edit(Workspace $workspace, MedicalPatient $patient)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $patient);

        return view('medical::dashboard.patients.edit', compact(
            'workspace',
            'patient'
        ));
    }

    public function update(Request $request, Workspace $workspace, MedicalPatient $patient)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $patient);

        $data = $this->validatedData($request, $workspace, $patient);

        $data['full_name'] = $this->makeFullName($data);

        $patient->update($data);

        return redirect()
            ->route('app.medical.patients.index', $workspace)
            ->with('success', 'تم تحديث بيانات المريض بنجاح.');
    }

    public function destroy(Workspace $workspace, MedicalPatient $patient)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $patient);

        $patient->delete();

        return back()->with('success', 'تم حذف المريض.');
    }

    private function validatedData(Request $request, Workspace $workspace, ?MedicalPatient $patient = null): array
    {
        return $request->validate([
            'patient_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('medical_patients', 'patient_code')
                    ->where('workspace_id', $workspace->id)
                    ->ignore($patient?->id),
            ],

            'first_name' => ['nullable', 'string', 'max:120'],
            'last_name' => ['nullable', 'string', 'max:120'],
            'full_name' => ['nullable', 'string', 'max:190'],

            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:190'],

            'gender' => ['required', Rule::in(['male', 'female', 'other', 'unknown'])],
            'birth_date' => ['nullable', 'date'],

            'national_id' => ['nullable', 'string', 'max:100'],

            'insurance_provider' => ['nullable', 'string', 'max:190'],
            'insurance_number' => ['nullable', 'string', 'max:190'],

            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:120'],
            'area' => ['nullable', 'string', 'max:120'],

            'emergency_contact_name' => ['nullable', 'string', 'max:190'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],

            'blood_type' => ['nullable', 'string', 'max:10'],
            'allergies' => ['nullable', 'string'],
            'chronic_diseases' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],

            'status' => ['required', Rule::in(['active', 'inactive', 'blocked'])],
        ]);
    }

    private function makeFullName(array $data): string
    {
        $firstName = trim((string) ($data['first_name'] ?? ''));
        $lastName = trim((string) ($data['last_name'] ?? ''));
        $fullName = trim((string) ($data['full_name'] ?? ''));

        if ($fullName !== '') {
            return $fullName;
        }

        $name = trim($firstName . ' ' . $lastName);

        return $name !== '' ? $name : 'مريض بدون اسم';
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

    private function ensureBelongsToWorkspace(Workspace $workspace, MedicalPatient $patient): void
    {
        abort_if((int) $patient->workspace_id !== (int) $workspace->id, 404);
    }
}