<?php

namespace Modules\Medical\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Medical\Models\MedicalStaff;
use Modules\Medical\Models\MedicalStaffService;

class MedicalStaffServiceController extends Controller
{
    public function edit(Workspace $workspace, MedicalStaff $staff)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $staff);

        $services = $workspace->medicalServices()
            ->with([
                'department:id,name',
                'specialty:id,name',
            ])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $assignedServices = MedicalStaffService::query()
            ->where('workspace_id', $workspace->id)
            ->where('staff_id', $staff->id)
            ->get()
            ->keyBy('service_id');

        return view('medical::dashboard.staff-services.edit', compact(
            'workspace',
            'staff',
            'services',
            'assignedServices'
        ));
    }

    public function update(Request $request, Workspace $workspace, MedicalStaff $staff)
    {
        abort_if($workspace->type !== 'medical', 404);
        $this->ensureBelongsToWorkspace($workspace, $staff);

        $data = $request->validate([
            'services' => ['nullable', 'array'],
            'services.*.enabled' => ['nullable', 'boolean'],
            'services.*.price_override' => ['nullable', 'numeric', 'min:0'],
            'services.*.duration_override' => ['nullable', 'integer', 'min:1', 'max:1440'],
        ]);

        $servicesInput = $data['services'] ?? [];

        $validServiceIds = $workspace->medicalServices()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        DB::transaction(function () use ($workspace, $staff, $servicesInput, $validServiceIds) {
            foreach ($servicesInput as $serviceId => $payload) {
                $serviceId = (int) $serviceId;

                if (! in_array($serviceId, $validServiceIds, true)) {
                    continue;
                }

                $enabled = (bool) ($payload['enabled'] ?? false);

                if (! $enabled) {
                    MedicalStaffService::query()
                        ->where('workspace_id', $workspace->id)
                        ->where('staff_id', $staff->id)
                        ->where('service_id', $serviceId)
                        ->delete();

                    continue;
                }

                MedicalStaffService::updateOrCreate(
                    [
                        'workspace_id' => $workspace->id,
                        'staff_id' => $staff->id,
                        'service_id' => $serviceId,
                    ],
                    [
                        'price_override' => $payload['price_override'] ?? null,
                        'duration_override' => $payload['duration_override'] ?? null,
                        'is_active' => true,
                    ]
                );
            }
        });

        return redirect()
            ->route('app.medical.staff.index', $workspace)
            ->with('success', 'تم تحديث خدمات عضو الفريق بنجاح.');
    }

    private function ensureBelongsToWorkspace(Workspace $workspace, MedicalStaff $staff): void
    {
        abort_if((int) $staff->workspace_id !== (int) $workspace->id, 404);
    }
}