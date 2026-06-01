<?php

namespace Modules\Medical\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Medical\Models\MedicalDepartment;

class MedicalDepartmentController extends Controller
{
    public function index(Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $departments = $workspace->medicalDepartments()
            ->with('branch:id,name')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15);

        return view('medical::dashboard.departments.index', compact(
            'workspace',
            'departments'
        ));
    }

    public function create(Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $branches = $workspace->medicalBranches()
            ->where('is_active', true)
            ->orderByDesc('is_main')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('medical::dashboard.departments.create', compact(
            'workspace',
            'branches'
        ));
    }

    public function store(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $data = $this->validatedData($request);

        $data['workspace_id'] = $workspace->id;
        $data['slug'] = $this->uniqueSlug($workspace, $data['name']);
        $data['is_active'] = $request->boolean('is_active');

        $workspace->medicalDepartments()->create($data);

        return redirect()
            ->route('app.medical.departments.index', $workspace)
            ->with('success', 'تم إضافة القسم بنجاح.');
    }

    public function edit(Workspace $workspace, MedicalDepartment $department)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $department);

        $branches = $workspace->medicalBranches()
            ->where('is_active', true)
            ->orderByDesc('is_main')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('medical::dashboard.departments.edit', compact(
            'workspace',
            'department',
            'branches'
        ));
    }

    public function update(Request $request, Workspace $workspace, MedicalDepartment $department)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $department);

        $data = $this->validatedData($request);
        $data['is_active'] = $request->boolean('is_active');

        $department->update($data);

        return redirect()
            ->route('app.medical.departments.index', $workspace)
            ->with('success', 'تم تحديث القسم بنجاح.');
    }

    public function destroy(Workspace $workspace, MedicalDepartment $department)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $department);

        $department->delete();

        return back()->with('success', 'تم حذف القسم.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'branch_id' => ['nullable', 'integer', 'exists:medical_branches,id'],
            'name' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function uniqueSlug(Workspace $workspace, string $name): string
    {
        $slug = Str::slug($name);

        if (! $slug) {
            $slug = 'department-' . Str::random(6);
        }

        $original = $slug;
        $counter = 1;

        while (
            MedicalDepartment::query()
                ->where('workspace_id', $workspace->id)
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function ensureBelongsToWorkspace(Workspace $workspace, MedicalDepartment $department): void
    {
        abort_if((int) $department->workspace_id !== (int) $workspace->id, 404);
    }
}