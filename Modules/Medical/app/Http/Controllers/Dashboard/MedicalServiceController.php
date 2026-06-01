<?php

namespace Modules\Medical\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Medical\Models\MedicalService;

class MedicalServiceController extends Controller
{
    public function index(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $services = $workspace->medicalServices()
            ->with([
                'branch:id,name',
                'department:id,name',
                'specialty:id,name',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->when($request->filled('department_id'), function ($query) use ($request) {
                $query->where('department_id', $request->input('department_id'));
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        $departments = $workspace->medicalDepartments()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('medical::dashboard.services.index', compact(
            'workspace',
            'services',
            'departments'
        ));
    }

    public function create(Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        return view('medical::dashboard.services.create', $this->formPayload($workspace));
    }

    public function store(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $data = $this->validatedData($request, $workspace);

        $data['workspace_id'] = $workspace->id;
        $data['slug'] = $this->uniqueSlug($workspace, $data['name']);

        $data = $this->normalizeBooleans($request, $data);

        $workspace->medicalServices()->create($data);

        return redirect()
            ->route('app.medical.services.index', $workspace)
            ->with('success', 'تم إضافة الخدمة بنجاح.');
    }

    public function edit(Workspace $workspace, MedicalService $service)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $service);

        return view('medical::dashboard.services.edit', array_merge(
            $this->formPayload($workspace),
            compact('service')
        ));
    }

    public function update(Request $request, Workspace $workspace, MedicalService $service)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $service);

        $data = $this->validatedData($request, $workspace, $service);

        $data = $this->normalizeBooleans($request, $data);

        $service->update($data);

        return redirect()
            ->route('app.medical.services.index', $workspace)
            ->with('success', 'تم تحديث الخدمة بنجاح.');
    }

    public function destroy(Workspace $workspace, MedicalService $service)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $service);

        $service->delete();

        return back()->with('success', 'تم حذف الخدمة.');
    }

    private function formPayload(Workspace $workspace): array
    {
        $branches = $workspace->medicalBranches()
            ->where('is_active', true)
            ->orderByDesc('is_main')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        $departments = $workspace->medicalDepartments()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        $specialties = $workspace->medicalSpecialties()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return compact(
            'workspace',
            'branches',
            'departments',
            'specialties'
        );
    }

    private function validatedData(Request $request, Workspace $workspace, ?MedicalService $service = null): array
    {
        return $request->validate([
            'branch_id' => [
                'nullable',
                Rule::exists('medical_branches', 'id')
                    ->where('workspace_id', $workspace->id),
            ],
            'department_id' => [
                'nullable',
                Rule::exists('medical_departments', 'id')
                    ->where('workspace_id', $workspace->id),
            ],
            'specialty_id' => [
                'nullable',
                Rule::exists('medical_specialties', 'id')
                    ->where('workspace_id', $workspace->id),
            ],

            'type' => [
                'required',
                Rule::in([
                    'consultation',
                    'follow_up',
                    'procedure',
                    'lab_test',
                    'scan',
                    'operation',
                    'session',
                    'package',
                    'other',
                ]),
            ],

            'name' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],

            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],

            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],

            'requires_doctor' => ['nullable', 'boolean'],
            'requires_appointment' => ['nullable', 'boolean'],
            'requires_sample' => ['nullable', 'boolean'],
            'requires_report' => ['nullable', 'boolean'],

            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function normalizeBooleans(Request $request, array $data): array
    {
        $data['requires_doctor'] = $request->boolean('requires_doctor');
        $data['requires_appointment'] = $request->boolean('requires_appointment');
        $data['requires_sample'] = $request->boolean('requires_sample');
        $data['requires_report'] = $request->boolean('requires_report');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function uniqueSlug(Workspace $workspace, string $name): string
    {
        $slug = Str::slug($name);

        if (! $slug) {
            $slug = 'service-' . Str::random(6);
        }

        $original = $slug;
        $counter = 1;

        while (
            MedicalService::query()
                ->where('workspace_id', $workspace->id)
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function ensureBelongsToWorkspace(Workspace $workspace, MedicalService $service): void
    {
        abort_if((int) $service->workspace_id !== (int) $workspace->id, 404);
    }
}