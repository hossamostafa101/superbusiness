<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\StoreBusinessAppointmentRequest;
use App\Http\Requests\App\UpdateBusinessAppointmentRequest;
use App\Models\BusinessAppointment;
use App\Models\Workspace;
use App\Services\App\BusinessAppointmentService;
use App\Services\Core\FeatureLimitService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BusinessAppointmentController extends Controller
{
    public function __construct(
        private readonly BusinessAppointmentService $businessAppointmentService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function index(Request $request, Workspace $workspace)
    {
        $appointments = $workspace->businessAppointments()
            ->with([
                'customer:id,name,phone,email',
                'service:id,name,duration_minutes,price,currency',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('appointment_date', '>=', $request->input('date_from'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('appointment_date', '<=', $request->input('date_to'));
            })
            ->orderByDesc('appointment_date')
            ->orderByDesc('start_time')
            ->paginate(15)
            ->withQueryString();

        $appointmentsLimit = $this->featureLimitService->limit($workspace, 'appointments_limit', 10);
        $isUnlimited = $appointmentsLimit === -1;

        return view('app.business-appointments.index', compact(
            'workspace',
            'appointments',
            'appointmentsLimit',
            'isUnlimited'
        ));
    }

    public function create(Workspace $workspace)
    {
        $currentCount = $workspace->businessAppointments()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'appointments_limit', $currentCount)) {
            return redirect()
                ->route('app.appointments.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى للمواعيد في باقتك الحالية.');
        }

        $customers = $workspace->businessCustomers()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'email']);

        $services = $workspace->businessServices()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'duration_minutes', 'price', 'currency']);

        return view('app.business-appointments.create', compact(
            'workspace',
            'customers',
            'services'
        ));
    }

    public function store(StoreBusinessAppointmentRequest $request, Workspace $workspace)
{
    $currentCount = $workspace->businessAppointments()->count();

    if (! $this->featureLimitService->canCreate($workspace, 'appointments_limit', $currentCount)) {
        return redirect()
            ->route('app.appointments.index', $workspace)
            ->with('error', 'وصلت للحد الأقصى للمواعيد في باقتك الحالية.');
    }

    try {
        $this->businessAppointmentService->create(
            workspace: $workspace,
            data: $request->validated()
        );

        return redirect()
            ->route('app.appointments.index', $workspace)
            ->with('success', 'تم إضافة الموعد بنجاح.');
    } catch (\Throwable $e) {
        return back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}
    public function edit(Workspace $workspace, BusinessAppointment $businessAppointment)
    {
        $this->ensureAppointmentBelongsToWorkspace($workspace, $businessAppointment);

        $customers = $workspace->businessCustomers()
            ->where(function ($query) use ($businessAppointment) {
                $query->where('status', 'active');

                if ($businessAppointment->customer_id) {
                    $query->orWhere('id', $businessAppointment->customer_id);
                }
            })
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'email']);

        $services = $workspace->businessServices()
            ->where(function ($query) use ($businessAppointment) {
                $query->where('is_active', true);

                if ($businessAppointment->service_id) {
                    $query->orWhere('id', $businessAppointment->service_id);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'duration_minutes', 'price', 'currency']);

        return view('app.business-appointments.edit', compact(
            'workspace',
            'businessAppointment',
            'customers',
            'services'
        ));
    }

   public function update(UpdateBusinessAppointmentRequest $request, Workspace $workspace, BusinessAppointment $businessAppointment)
{
    $this->ensureAppointmentBelongsToWorkspace($workspace, $businessAppointment);

    try {
        $this->businessAppointmentService->update(
            appointment: $businessAppointment,
            workspace: $workspace,
            data: $request->validated()
        );

        return redirect()
            ->route('app.appointments.index', $workspace)
            ->with('success', 'تم تحديث الموعد بنجاح.');
    } catch (\Throwable $e) {
        return back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}

    public function destroy(Workspace $workspace, BusinessAppointment $businessAppointment)
    {
        $this->ensureAppointmentBelongsToWorkspace($workspace, $businessAppointment);

        $this->businessAppointmentService->delete($businessAppointment);

        return redirect()
            ->route('app.appointments.index', $workspace)
            ->with('success', 'تم حذف الموعد بنجاح.');
    }

    public function updateStatus(Request $request, Workspace $workspace, BusinessAppointment $businessAppointment)
    {
        $this->ensureAppointmentBelongsToWorkspace($workspace, $businessAppointment);

        $data = $request->validate([
            'status' => [
                'required',
                Rule::in(['pending', 'confirmed', 'completed', 'cancelled', 'no_show']),
            ],
        ]);

        $businessAppointment->update([
            'status' => $data['status'],
        ]);

        return back()->with('success', 'تم تحديث حالة الموعد.');
    }

    private function ensureAppointmentBelongsToWorkspace(Workspace $workspace, BusinessAppointment $businessAppointment): void
    {
        abort_if((int) $businessAppointment->workspace_id !== (int) $workspace->id, 404);
    }






    public function calendar(Workspace $workspace)
{
    return view('app.business-appointments.calendar', compact('workspace'));
}

public function calendarEvents(Request $request, Workspace $workspace)
{
    $start = $request->input('start');
    $end = $request->input('end');

    $appointments = $workspace->businessAppointments()
        ->with([
            'customer:id,name,phone',
            'service:id,name',
        ])
        ->when($start, function ($query) use ($start) {
            $query->whereDate('appointment_date', '>=', $start);
        })
        ->when($end, function ($query) use ($end) {
            $query->whereDate('appointment_date', '<=', $end);
        })
        ->get();

    return response()->json(
        $appointments->map(function ($appointment) use ($workspace) {
            $titleParts = [];

            $customerName = $appointment->customer?->name
                ?: $appointment->customer_name
                ?: 'عميل';

            $titleParts[] = $customerName;

            if ($appointment->service?->name) {
                $titleParts[] = $appointment->service->name;
            }

            $startDateTime = $appointment->appointment_date->format('Y-m-d')
                . 'T'
                . substr($appointment->start_time, 0, 5);

            $endDateTime = null;

            if ($appointment->end_time) {
                $endDateTime = $appointment->appointment_date->format('Y-m-d')
                    . 'T'
                    . substr($appointment->end_time, 0, 5);
            }

            return [
                'id' => $appointment->id,
                'title' => implode(' - ', $titleParts),
                'start' => $startDateTime,
                'end' => $endDateTime,
                'url' => route('app.appointments.edit', [$workspace, $appointment]),
                'backgroundColor' => $this->calendarColor($appointment->status),
                'borderColor' => $this->calendarColor($appointment->status),
                'extendedProps' => [
                    'status' => $appointment->status,
                    'phone' => $appointment->customer?->phone ?: $appointment->customer_phone,
                    'source' => $appointment->source,
                ],
            ];
        })->values()
    );
}

private function calendarColor(string $status): string
{
    return match ($status) {
        'pending' => '#f59e0b',
        'confirmed' => '#2563eb',
        'completed' => '#16a34a',
        'cancelled' => '#dc2626',
        'no_show' => '#111827',
        default => '#6b7280',
    };
}
}