<?php

namespace Modules\Medical\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Medical\Models\MedicalBranch;

class MedicalBranchController extends Controller
{
    public function index(Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $branches = $workspace->medicalBranches()
            ->orderByDesc('is_main')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15);

        return view('medical::dashboard.branches.index', compact(
            'workspace',
            'branches'
        ));
    }

    public function create(Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        return view('medical::dashboard.branches.create', compact('workspace'));
    }

    public function store(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $data = $this->validatedData($request, $workspace);

        $data['slug'] = $this->uniqueSlug($workspace, $data['name']);
        $data['is_main'] = $request->boolean('is_main');
        $data['is_active'] = $request->boolean('is_active');

        if ($data['is_main']) {
            $workspace->medicalBranches()->update(['is_main' => false]);
        }

        $workspace->medicalBranches()->create($data);

        return redirect()
            ->route('app.medical.branches.index', $workspace)
            ->with('success', 'تم إضافة الفرع بنجاح.');
    }

    public function edit(Workspace $workspace, MedicalBranch $branch)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $branch);

        return view('medical::dashboard.branches.edit', compact(
            'workspace',
            'branch'
        ));
    }

    public function update(Request $request, Workspace $workspace, MedicalBranch $branch)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $branch);

        $data = $this->validatedData($request, $workspace, $branch);

        $data['is_main'] = $request->boolean('is_main');
        $data['is_active'] = $request->boolean('is_active');

        if ($data['is_main']) {
            $workspace->medicalBranches()
                ->whereKeyNot($branch->id)
                ->update(['is_main' => false]);
        }

        $branch->update($data);

        return redirect()
            ->route('app.medical.branches.index', $workspace)
            ->with('success', 'تم تحديث الفرع بنجاح.');
    }

    public function destroy(Workspace $workspace, MedicalBranch $branch)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $branch);

        $branch->delete();

        return back()->with('success', 'تم حذف الفرع.');
    }

    private function validatedData(Request $request, Workspace $workspace, ?MedicalBranch $branch = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:190'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:120'],
            'area' => ['nullable', 'string', 'max:120'],
            'google_maps_url' => ['nullable', 'url'],
            'is_main' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function uniqueSlug(Workspace $workspace, string $name): string
    {
        $slug = Str::slug($name);

        if (! $slug) {
            $slug = 'branch-' . Str::random(6);
        }

        $original = $slug;
        $counter = 1;

        while (
            MedicalBranch::query()
                ->where('workspace_id', $workspace->id)
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function ensureBelongsToWorkspace(Workspace $workspace, MedicalBranch $branch): void
    {
        abort_if((int) $branch->workspace_id !== (int) $workspace->id, 404);
    }
}