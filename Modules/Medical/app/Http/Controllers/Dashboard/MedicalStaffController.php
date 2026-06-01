<?php

namespace Modules\Medical\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Medical\Models\MedicalStaff;

class MedicalStaffController extends Controller
{
    public function index(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $staff = $workspace->medicalStaff()
            ->with([
                'branch:id,name',
                'department:id,name',
                'specialty:id,name',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->where('role', $request->input('role'));
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

        return view('medical::dashboard.staff.index', compact(
            'workspace',
            'staff',
            'departments'
        ));
    }

    public function create(Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        return view('medical::dashboard.staff.create', $this->formPayload($workspace));
    }

    public function store(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $data = $this->validatedData($request, $workspace);

        $data['workspace_id'] = $workspace->id;
        $data['slug'] = $this->uniqueSlug($workspace, $data['name']);

        $data = $this->normalizeBooleans($request, $data);

        $workspace->medicalStaff()->create($data);

        return redirect()
            ->route('app.medical.staff.index', $workspace)
            ->with('success', 'تم إضافة عضو الفريق بنجاح.');
    }

    public function edit(Workspace $workspace, MedicalStaff $staff)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $staff);

        return view('medical::dashboard.staff.edit', array_merge(
            $this->formPayload($workspace),
            compact('staff')
        ));
    }

    public function update(Request $request, Workspace $workspace, MedicalStaff $staff)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $staff);

        $data = $this->validatedData($request, $workspace, $staff);

        $data = $this->normalizeBooleans($request, $data);

        $staff->update($data);

        return redirect()
            ->route('app.medical.staff.index', $workspace)
            ->with('success', 'تم تحديث بيانات عضو الفريق بنجاح.');
    }

    public function destroy(Workspace $workspace, MedicalStaff $staff)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $staff);

        $staff->delete();

        return back()->with('success', 'تم حذف عضو الفريق.');
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

        $workspaceUsers = $workspace->users()
            ->orderBy('name')
            ->get(['users.id', 'users.name', 'users.email']);

        return compact(
            'workspace',
            'branches',
            'departments',
            'specialties',
            'workspaceUsers'
        );
    }

    private function validatedData(Request $request, Workspace $workspace, ?MedicalStaff $staff = null): array
    {
        return $request->validate([
            'user_id' => [
                'nullable',
                Rule::exists('users', 'id'),
            ],

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

            'role' => [
                'required',
                Rule::in([
                    'doctor',
                    'nurse',
                    'lab_technician',
                    'radiology_technician',
                    'receptionist',
                    'accountant',
                    'admin',
                    'other',
                ]),
            ],

            'name' => ['required', 'string', 'max:190'],
            'title' => ['nullable', 'string', 'max:190'],
            'bio' => ['nullable', 'string'],

            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:190'],

            'consultation_fee' => ['nullable', 'numeric', 'min:0'],
            'follow_up_fee' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],

            'default_slot_minutes' => ['nullable', 'integer', 'min:5', 'max:240'],

            'accepts_online_booking' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function normalizeBooleans(Request $request, array $data): array
    {
        $data['accepts_online_booking'] = $request->boolean('accepts_online_booking');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function uniqueSlug(Workspace $workspace, string $name): string
    {
        $slug = Str::slug($name);

        if (! $slug) {
            $slug = 'staff-' . Str::random(6);
        }

        $original = $slug;
        $counter = 1;

        while (
            MedicalStaff::query()
                ->where('workspace_id', $workspace->id)
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function ensureBelongsToWorkspace(Workspace $workspace, MedicalStaff $staff): void
    {
        abort_if((int) $staff->workspace_id !== (int) $workspace->id, 404);
    }
}