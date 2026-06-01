<?php

namespace Modules\Medical\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Medical\Models\MedicalSpecialty;

class MedicalSpecialtyController extends Controller
{
    public function index(Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $specialties = $workspace->medicalSpecialties()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15);

        return view('medical::dashboard.specialties.index', compact(
            'workspace',
            'specialties'
        ));
    }

    public function create(Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        return view('medical::dashboard.specialties.create', compact('workspace'));
    }

    public function store(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $data = $this->validatedData($request);

        $data['workspace_id'] = $workspace->id;
        $data['slug'] = $this->uniqueSlug($workspace, $data['name']);
        $data['is_active'] = $request->boolean('is_active');

        $workspace->medicalSpecialties()->create($data);

        return redirect()
            ->route('app.medical.specialties.index', $workspace)
            ->with('success', 'تم إضافة التخصص بنجاح.');
    }

    public function edit(Workspace $workspace, MedicalSpecialty $specialty)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $specialty);

        return view('medical::dashboard.specialties.edit', compact(
            'workspace',
            'specialty'
        ));
    }

    public function update(Request $request, Workspace $workspace, MedicalSpecialty $specialty)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $specialty);

        $data = $this->validatedData($request);
        $data['is_active'] = $request->boolean('is_active');

        $specialty->update($data);

        return redirect()
            ->route('app.medical.specialties.index', $workspace)
            ->with('success', 'تم تحديث التخصص بنجاح.');
    }

    public function destroy(Workspace $workspace, MedicalSpecialty $specialty)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $specialty);

        $specialty->delete();

        return back()->with('success', 'تم حذف التخصص.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function uniqueSlug(Workspace $workspace, string $name): string
    {
        $slug = Str::slug($name);

        if (! $slug) {
            $slug = 'specialty-' . Str::random(6);
        }

        $original = $slug;
        $counter = 1;

        while (
            MedicalSpecialty::query()
                ->where('workspace_id', $workspace->id)
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function ensureBelongsToWorkspace(Workspace $workspace, MedicalSpecialty $specialty): void
    {
        abort_if((int) $specialty->workspace_id !== (int) $workspace->id, 404);
    }
}