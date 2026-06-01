<?php

namespace Modules\Medical\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Medical\Models\MedicalAppointment;

class MedicalAppointmentBoardController extends Controller
{
    public function index(Request $request, Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $date = $request->input('date', today()->format('Y-m-d'));

        $appointments = $workspace->medicalAppointments()
            ->with([
                'branch:id,name',
                'patient:id,full_name,phone,patient_code',
                'service:id,name',
                'staff:id,name',
            ])
            ->whereDate('appointment_date', $date)
            ->when($request->filled('staff_id'), function ($query) use ($request) {
                $query->where('staff_id', $request->input('staff_id'));
            })
            ->when($request->filled('branch_id'), function ($query) use ($request) {
                $query->where('branch_id', $request->input('branch_id'));
            })
            ->orderBy('starts_at')
            ->get()
            ->groupBy('status');

        $staffMembers = $workspace->medicalStaff()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        $branches = $workspace->medicalBranches()
            ->where('is_active', true)
            ->orderByDesc('is_main')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        $statuses = [
            'pending' => [
                'label' => 'في الانتظار',
                'class' => 'warning',
            ],
            'confirmed' => [
                'label' => 'مؤكد',
                'class' => 'primary',
            ],
            'checked_in' => [
                'label' => 'وصل',
                'class' => 'info',
            ],
            'in_progress' => [
                'label' => 'جاري الكشف',
                'class' => 'dark',
            ],
            'completed' => [
                'label' => 'مكتمل',
                'class' => 'success',
            ],
            'cancelled' => [
                'label' => 'ملغي',
                'class' => 'danger',
            ],
            'no_show' => [
                'label' => 'لم يحضر',
                'class' => 'secondary',
            ],
        ];

        return view('medical::dashboard.appointments-board.index', compact(
            'workspace',
            'date',
            'appointments',
            'statuses',
            'staffMembers',
            'branches'
        ));
    }

    public function updateStatus(Request $request, Workspace $workspace, MedicalAppointment $appointment)
    {
        abort_if($workspace->type !== 'medical', 404);
        abort_if((int) $appointment->workspace_id !== (int) $workspace->id, 404);

        $data = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'confirmed',
                    'checked_in',
                    'in_progress',
                    'completed',
                    'cancelled',
                    'no_show',
                ]),
            ],
        ]);

        $payload = [
            'status' => $data['status'],
        ];

        if ($data['status'] === 'cancelled') {
            $payload['cancelled_at'] = now();
        }

        $appointment->update($payload);

        return back()->with('success', 'تم تحديث حالة الحجز.');
    }
}