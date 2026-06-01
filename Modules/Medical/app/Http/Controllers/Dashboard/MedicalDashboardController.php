<?php

namespace Modules\Medical\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Modules\Medical\Models\MedicalAppointment;

class MedicalDashboardController extends Controller
{
    public function index(Workspace $workspace)
    {
        abort_if($workspace->type !== 'medical', 404);

        $today = today();

        $settings = $workspace->medicalSetting()->first();

        $todayAppointmentsQuery = $workspace->medicalAppointments()
            ->whereDate('appointment_date', $today);

        $stats = [
            'today_total' => (clone $todayAppointmentsQuery)->count(),
            'pending' => (clone $todayAppointmentsQuery)->where('status', 'pending')->count(),
            'confirmed' => (clone $todayAppointmentsQuery)->where('status', 'confirmed')->count(),
            'checked_in' => (clone $todayAppointmentsQuery)->where('status', 'checked_in')->count(),
            'in_progress' => (clone $todayAppointmentsQuery)->where('status', 'in_progress')->count(),
            'completed' => (clone $todayAppointmentsQuery)->where('status', 'completed')->count(),

            'patients' => $workspace->medicalPatients()->count(),
            'active_staff' => $workspace->medicalStaff()->where('is_active', true)->count(),
            'active_services' => $workspace->medicalServices()->where('is_active', true)->count(),
            'branches' => $workspace->medicalBranches()->where('is_active', true)->count(),
        ];

        $upcomingAppointments = $workspace->medicalAppointments()
            ->with([
                'patient:id,full_name,phone',
                'service:id,name',
                'staff:id,name',
                'branch:id,name',
            ])
            ->whereDate('appointment_date', $today)
            ->whereNotIn('status', ['completed', 'cancelled', 'no_show'])
            ->orderBy('starts_at')
            ->limit(8)
            ->get();

        $latestPatients = $workspace->medicalPatients()
            ->latest('id')
            ->limit(5)
            ->get(['id', 'full_name', 'phone', 'patient_code', 'created_at']);

        return view('medical::dashboard.index', compact(
            'workspace',
            'settings',
            'stats',
            'upcomingAppointments',
            'latestPatients'
        ));
    }
}