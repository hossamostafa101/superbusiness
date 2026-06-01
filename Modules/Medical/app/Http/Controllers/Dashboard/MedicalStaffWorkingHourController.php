<?php

namespace Modules\Medical\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Medical\Models\MedicalStaff;
use Modules\Medical\Models\MedicalStaffWorkingHour;

class MedicalStaffWorkingHourController extends Controller
{
    public function edit(Workspace $workspace, MedicalStaff $staff)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $staff);

        $branches = $workspace->medicalBranches()
            ->where('is_active', true)
            ->orderByDesc('is_main')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        $workingHours = $staff->workingHours()
            ->get()
            ->groupBy('day_of_week');

        $days = $this->days();

        return view('medical::dashboard.staff-working-hours.edit', compact(
            'workspace',
            'staff',
            'branches',
            'workingHours',
            'days'
        ));
    }

    public function update(Request $request, Workspace $workspace, MedicalStaff $staff)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $staff);

        $data = $request->validate([
            'hours' => ['nullable', 'array'],

            'hours.*.enabled' => ['nullable', 'boolean'],
            'hours.*.branch_id' => ['nullable', 'integer'],
            'hours.*.starts_at' => ['nullable', 'date_format:H:i'],
            'hours.*.ends_at' => ['nullable', 'date_format:H:i'],
            'hours.*.slot_minutes' => ['nullable', 'integer', 'min:5', 'max:240'],
        ]);

        $validBranchIds = $workspace->medicalBranches()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        DB::transaction(function () use ($workspace, $staff, $data, $validBranchIds) {
            MedicalStaffWorkingHour::query()
                ->where('workspace_id', $workspace->id)
                ->where('staff_id', $staff->id)
                ->delete();

            foreach (($data['hours'] ?? []) as $dayOfWeek => $payload) {
                $dayOfWeek = (int) $dayOfWeek;

                if ($dayOfWeek < 0 || $dayOfWeek > 6) {
                    continue;
                }

                $enabled = (bool) ($payload['enabled'] ?? false);

                if (! $enabled) {
                    continue;
                }

                $startsAt = $payload['starts_at'] ?? null;
                $endsAt = $payload['ends_at'] ?? null;

                if (! $startsAt || ! $endsAt) {
                    continue;
                }

                if ($startsAt >= $endsAt) {
                    continue;
                }

                $branchId = $payload['branch_id'] ?? null;
                $branchId = $branchId ? (int) $branchId : null;

                if ($branchId && ! in_array($branchId, $validBranchIds, true)) {
                    $branchId = null;
                }

                MedicalStaffWorkingHour::create([
                    'workspace_id' => $workspace->id,
                    'staff_id' => $staff->id,
                    'branch_id' => $branchId,
                    'day_of_week' => $dayOfWeek,
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'slot_minutes' => $payload['slot_minutes'] ?? $staff->default_slot_minutes ?? 30,
                    'is_active' => true,
                ]);
            }
        });

        return redirect()
            ->route('app.medical.staff.index', $workspace)
            ->with('success', 'تم تحديث مواعيد العمل بنجاح.');
    }

    private function days(): array
    {
        return [
            0 => 'الأحد',
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
        ];
    }

    private function ensureBelongsToWorkspace(Workspace $workspace, MedicalStaff $staff): void
    {
        abort_if((int) $staff->workspace_id !== (int) $workspace->id, 404);
    }
}