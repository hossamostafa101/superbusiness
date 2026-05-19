<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Models\RestaurantMenu\RestaurantInvoice;
use App\Models\Workspace;
use App\Services\App\RestaurantMenu\RestaurantInvoiceService;
use App\Services\Public\RestaurantMenu\RestaurantMenuSettingReader;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RestaurantInvoiceController extends Controller
{
    public function __construct(
        private readonly RestaurantInvoiceService $restaurantInvoiceService,
        private readonly RestaurantMenuSettingReader $settingReader
    ) {}

    public function index(Request $request, Workspace $workspace)
    {
        $invoices = $workspace->restaurantInvoices()
            ->with([
                'branch:id,name',
                'table:id,name,number',
            ])
            ->withCount([
                'items',
                'guests',
                'orders',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                        ->orWhere('table_number', 'like', "%{$search}%")
                        ->orWhere('opened_by_name', 'like', "%{$search}%")
                        ->orWhere('opened_by_phone', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('branch_id'), function ($query) use ($request) {
                $query->where('branch_id', $request->input('branch_id'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->input('date_from'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->input('date_to'));
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $branches = $workspace->restaurantBranches()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name']);

        $extendMinutesStep = $this->settingReader->extendMinutesStep($workspace);

        return view('app.restaurant-menu.invoices.index', compact(
            'workspace',
            'invoices',
            'branches',
            'extendMinutesStep'
        ));
    }

    public function show(Workspace $workspace, RestaurantInvoice $restaurantInvoice)
    {
        $this->ensureInvoiceBelongsToWorkspace($workspace, $restaurantInvoice);

        $restaurantInvoice->load([
            'branch',
            'table',
            'guests',
            'orders',
            'items' => function ($query) {
                $query->with(['options', 'guest'])
                    ->orderBy('id');
            },
        ]);

        $extendMinutesStep = $this->settingReader->extendMinutesStep($workspace);

        return view('app.restaurant-menu.invoices.show', compact(
            'workspace',
            'restaurantInvoice',
            'extendMinutesStep'
        ));
    }

    public function updateStatus(Request $request, Workspace $workspace, RestaurantInvoice $restaurantInvoice)
    {
        $this->ensureInvoiceBelongsToWorkspace($workspace, $restaurantInvoice);

        $data = $request->validate([
            'status' => [
                'required',
                Rule::in(['open', 'closed', 'expired', 'cancelled']),
            ],
        ]);

        match ($data['status']) {
            'closed' => $this->restaurantInvoiceService->close($restaurantInvoice),
            'expired' => $this->restaurantInvoiceService->expire($restaurantInvoice),
            'cancelled' => $this->restaurantInvoiceService->cancel($restaurantInvoice),
            default => $restaurantInvoice->update([
                'status' => 'open',
                'closed_at' => null,
                'last_activity_at' => now(),
            ]),
        };

        return back()->with('success', 'تم تحديث حالة الفاتورة بنجاح.');
    }

    public function extend(Request $request, Workspace $workspace, RestaurantInvoice $restaurantInvoice)
    {
        $this->ensureInvoiceBelongsToWorkspace($workspace, $restaurantInvoice);

        $data = $request->validate([
            'minutes' => ['required', 'integer', 'min:5', 'max:1440'],
        ]);

        if ($restaurantInvoice->status !== 'open') {
            return back()->with('error', 'لا يمكن تمديد فاتورة غير مفتوحة.');
        }

        $this->restaurantInvoiceService->extend(
            invoice: $restaurantInvoice,
            minutes: (int) $data['minutes']
        );

        return back()->with('success', 'تم تمديد وقت الفاتورة بنجاح.');
    }

    public function recalculate(Workspace $workspace, RestaurantInvoice $restaurantInvoice)
    {
        $this->ensureInvoiceBelongsToWorkspace($workspace, $restaurantInvoice);

        $this->restaurantInvoiceService->recalculate($restaurantInvoice);

        return back()->with('success', 'تم تحديث إجمالي الفاتورة.');
    }

    private function ensureInvoiceBelongsToWorkspace(Workspace $workspace, RestaurantInvoice $restaurantInvoice): void
    {
        abort_if((int) $restaurantInvoice->workspace_id !== (int) $workspace->id, 404);
    }
}