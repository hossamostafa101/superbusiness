<?php

namespace Modules\Medical\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Medical\Models\MedicalVisit;
use Modules\Medical\Models\MedicalVisitNote;

class MedicalVisitNoteController extends Controller
{
    public function store(Request $request, Workspace $workspace, MedicalVisit $visit)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureVisitBelongsToWorkspace($workspace, $visit);

        $data = $request->validate([
            'type' => [
                'required',
                Rule::in([
                    'general',
                    'complaint',
                    'diagnosis',
                    'treatment',
                    'follow_up',
                    'internal',
                ]),
            ],
            'note' => ['required', 'string'],
            'staff_id' => [
                'nullable',
                Rule::exists('medical_staff', 'id')
                    ->where('workspace_id', $workspace->id),
            ],
        ]);

        $visit->visitNotes()->create([
            'workspace_id' => $workspace->id,
            'staff_id' => $data['staff_id'] ?? $visit->staff_id,
            'type' => $data['type'],
            'note' => $data['note'],
        ]);

        return back()->with('success', 'تم إضافة الملاحظة.');
    }

    public function destroy(Workspace $workspace, MedicalVisit $visit, MedicalVisitNote $note)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureVisitBelongsToWorkspace($workspace, $visit);

        abort_if((int) $note->visit_id !== (int) $visit->id, 404);
        abort_if((int) $note->workspace_id !== (int) $workspace->id, 404);

        $note->delete();

        return back()->with('success', 'تم حذف الملاحظة.');
    }

    private function ensureVisitBelongsToWorkspace(Workspace $workspace, MedicalVisit $visit): void
    {
        abort_if((int) $visit->workspace_id !== (int) $workspace->id, 404);
    }
}