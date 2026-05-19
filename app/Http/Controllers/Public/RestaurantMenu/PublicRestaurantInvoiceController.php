<?php

namespace App\Http\Controllers\Public\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\RestaurantMenu\JoinRestaurantInvoiceRequest;
use App\Http\Requests\Public\RestaurantMenu\OpenRestaurantInvoiceRequest;
use App\Models\RestaurantMenu\RestaurantBranch;
use App\Models\RestaurantMenu\RestaurantInvoice;
use App\Models\RestaurantMenu\RestaurantTable;
use App\Models\Workspace;
use App\Services\Core\FeatureLimitService;
use App\Services\Public\RestaurantMenu\PublicRestaurantInvoiceService;
use App\Services\Public\RestaurantMenu\RestaurantInvoicePinService;
use App\Services\Public\RestaurantMenu\RestaurantInvoiceSessionService;
use App\Services\Public\RestaurantMenu\RestaurantMenuSettingReader;
use Illuminate\Http\Request;

class PublicRestaurantInvoiceController extends Controller
{
    public function __construct(
        private readonly PublicRestaurantInvoiceService $invoiceService,
        private readonly RestaurantInvoicePinService $pinService,
        private readonly RestaurantInvoiceSessionService $sessionService,
        private readonly RestaurantMenuSettingReader $settingReader,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function open(
        OpenRestaurantInvoiceRequest $request,
        Workspace $workspace,
        RestaurantBranch $branch
    ) {
        abort_if($workspace->status !== 'active', 404);
        abort_if((int) $branch->workspace_id !== (int) $workspace->id, 404);
        abort_if(! $branch->is_active, 404);

        if (! $this->featureLimitService->enabled($workspace, 'restaurant_open_invoice_enabled')) {
            return back()->withInput()->with('error', 'الفواتير المفتوحة غير متاحة حاليًا.');
        }

        if ($this->settingReader->orderingMode($workspace) !== 'open_invoice') {
            return back()->withInput()->with('error', 'نظام الفاتورة المفتوحة غير مفعل لهذا المطعم.');
        }

        $table = $this->resolveTable($workspace, $branch, $request->input('table_code'));

        $openInvoice = $this->sessionService->findOpenInvoiceForTable(
            workspace: $workspace,
            branch: $branch,
            table: $table
        );

        if ($openInvoice && ! $this->settingReader->allowNewInvoiceWhenTableBusy($workspace)) {
            return back()
                ->withInput()
                ->with('error', 'هذه الطاولة لديها فاتورة مفتوحة حاليًا.');
        }

        $result = $this->invoiceService->openInvoice(
            workspace: $workspace,
            branch: $branch,
            table: $table,
            data: $request->validated()
        );

        return redirect()
            ->route('public.restaurant-menu.branch', [
                'workspace' => $workspace,
                'branch' => $branch,
                'invoice_id' => $result['invoice']->id,
            ])
            ->with('invoice_pin', $result['pin_display'])
            ->with('success', 'تم فتح الفاتورة بنجاح.');
    }

    public function join(
        JoinRestaurantInvoiceRequest $request,
        Workspace $workspace,
        RestaurantBranch $branch,
        RestaurantInvoice $restaurantInvoice
    ) {
        abort_if($workspace->status !== 'active', 404);
        abort_if((int) $branch->workspace_id !== (int) $workspace->id, 404);
        abort_if((int) $restaurantInvoice->workspace_id !== (int) $workspace->id, 404);
        abort_if((int) $restaurantInvoice->branch_id !== (int) $branch->id, 404);

        if (! $restaurantInvoice->isOpen() || $restaurantInvoice->isExpired()) {
            return back()->withInput()->with('error', 'هذه الفاتورة غير متاحة أو انتهت مدتها.');
        }

        if ($this->settingReader->joinPolicy($workspace) === 'block_until_closed') {
            return back()->withInput()->with('error', 'لا يمكن الانضمام لهذه الفاتورة حسب إعدادات المطعم.');
        }

        if (! $this->featureLimitService->enabled($workspace, 'restaurant_invoice_join_with_pin_enabled')) {
            return back()->withInput()->with('error', 'الانضمام للفاتورة غير متاح في الباقة الحالية.');
        }

        if (! $this->pinService->verify($request->input('pin'), $restaurantInvoice->pin_hash)) {
            return back()->withInput()->with('error', 'رقم PIN غير صحيح.');
        }

        $this->invoiceService->joinInvoice(
            invoice: $restaurantInvoice,
            data: $request->validated()
        );

        return redirect()
            ->route('public.restaurant-menu.branch', [
                'workspace' => $workspace,
                'branch' => $branch,
                'invoice_id' => $restaurantInvoice->id,
            ])
            ->with('success', 'تم الانضمام إلى الفاتورة بنجاح.');
    }

    public function show(
        Workspace $workspace,
        RestaurantBranch $branch,
        RestaurantInvoice $restaurantInvoice
    ) {
        abort_if($workspace->status !== 'active', 404);
        abort_if((int) $branch->workspace_id !== (int) $workspace->id, 404);
        abort_if((int) $restaurantInvoice->workspace_id !== (int) $workspace->id, 404);
        abort_if((int) $restaurantInvoice->branch_id !== (int) $branch->id, 404);

        $restaurantInvoice->load([
            'branch',
            'table',
            'guests',
            'items.options',
            'orders',
        ]);

        return view('public.restaurant-menu.invoice-show', compact(
            'workspace',
            'branch',
            'restaurantInvoice'
        ));
    }

    private function resolveTable(
        Workspace $workspace,
        RestaurantBranch $branch,
        ?string $tableCode
    ): ?RestaurantTable {
        if (! $tableCode) {
            return null;
        }

        return $workspace->restaurantTables()
            ->where('branch_id', $branch->id)
            ->where('code', $tableCode)
            ->where('is_active', true)
            ->first();
    }
}