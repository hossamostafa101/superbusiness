<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\StoreBusinessCustomerRequest;
use App\Http\Requests\App\UpdateBusinessCustomerRequest;
use App\Models\BusinessCustomer;
use App\Models\Workspace;
use App\Services\App\BusinessCustomerService;
use App\Services\Core\FeatureLimitService;
use Illuminate\Http\Request;

class BusinessCustomerController extends Controller
{
    public function __construct(
        private readonly BusinessCustomerService $businessCustomerService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function index(Request $request, Workspace $workspace)
    {
        $customers = $workspace->businessCustomers()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('source'), function ($query) use ($request) {
                $query->where('source', $request->input('source'));
            })
            ->withCount('appointments')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $customersLimit = $this->featureLimitService->limit($workspace, 'customers_limit', 20);
        $isUnlimited = $customersLimit === -1;

        return view('app.business-customers.index', compact(
            'workspace',
            'customers',
            'customersLimit',
            'isUnlimited'
        ));
    }

    public function create(Workspace $workspace)
    {
        $currentCount = $workspace->businessCustomers()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'customers_limit', $currentCount)) {
            return redirect()
                ->route('app.customers.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى للعملاء في باقتك الحالية.');
        }

        return view('app.business-customers.create', compact('workspace'));
    }

    public function store(StoreBusinessCustomerRequest $request, Workspace $workspace)
    {
        $currentCount = $workspace->businessCustomers()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'customers_limit', $currentCount)) {
            return redirect()
                ->route('app.customers.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى للعملاء في باقتك الحالية.');
        }

        $this->businessCustomerService->create(
            workspace: $workspace,
            data: $request->validated()
        );

        return redirect()
            ->route('app.customers.index', $workspace)
            ->with('success', 'تم إضافة العميل بنجاح.');
    }

    public function edit(Workspace $workspace, BusinessCustomer $businessCustomer)
    {
        $this->ensureCustomerBelongsToWorkspace($workspace, $businessCustomer);

        return view('app.business-customers.edit', compact('workspace', 'businessCustomer'));
    }

    public function update(UpdateBusinessCustomerRequest $request, Workspace $workspace, BusinessCustomer $businessCustomer)
    {
        $this->ensureCustomerBelongsToWorkspace($workspace, $businessCustomer);

        $this->businessCustomerService->update(
            customer: $businessCustomer,
            data: $request->validated()
        );

        return redirect()
            ->route('app.customers.index', $workspace)
            ->with('success', 'تم تحديث بيانات العميل بنجاح.');
    }

    public function destroy(Workspace $workspace, BusinessCustomer $businessCustomer)
    {
        $this->ensureCustomerBelongsToWorkspace($workspace, $businessCustomer);

        $this->businessCustomerService->delete($businessCustomer);

        return redirect()
            ->route('app.customers.index', $workspace)
            ->with('success', 'تم حذف العميل بنجاح.');
    }

    private function ensureCustomerBelongsToWorkspace(Workspace $workspace, BusinessCustomer $businessCustomer): void
    {
        abort_if((int) $businessCustomer->workspace_id !== (int) $workspace->id, 404);
    }
}