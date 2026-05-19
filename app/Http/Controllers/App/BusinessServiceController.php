<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\StoreBusinessServiceRequest;
use App\Http\Requests\App\UpdateBusinessServiceRequest;
use App\Models\BusinessService;
use App\Models\Workspace;
use App\Services\App\BusinessServiceService;
use App\Services\Core\FeatureLimitService;
use Illuminate\Http\Request;

class BusinessServiceController extends Controller
{
    public function __construct(
        private readonly BusinessServiceService $businessServiceService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function index(Request $request, Workspace $workspace)
    {
        $services = $workspace->businessServices()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->input('status') === 'active') {
                    $query->where('is_active', true);
                }

                if ($request->input('status') === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->withCount('appointments')
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $servicesLimit = $this->featureLimitService->limit($workspace, 'services_limit', 3);
        $isUnlimited = $servicesLimit === -1;

        return view('app.business-services.index', compact(
            'workspace',
            'services',
            'servicesLimit',
            'isUnlimited'
        ));
    }

    public function create(Workspace $workspace)
    {
        $currentCount = $workspace->businessServices()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'services_limit', $currentCount)) {
            return redirect()
                ->route('app.services.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى للخدمات في باقتك الحالية.');
        }

        return view('app.business-services.create', compact('workspace'));
    }

    public function store(StoreBusinessServiceRequest $request, Workspace $workspace)
    {
        $currentCount = $workspace->businessServices()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'services_limit', $currentCount)) {
            return redirect()
                ->route('app.services.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى للخدمات في باقتك الحالية.');
        }

        $this->businessServiceService->create(
            workspace: $workspace,
            data: $request->validated()
        );

        return redirect()
            ->route('app.services.index', $workspace)
            ->with('success', 'تم إضافة الخدمة بنجاح.');
    }

    public function edit(Workspace $workspace, BusinessService $businessService)
    {
        $this->ensureServiceBelongsToWorkspace($workspace, $businessService);

        return view('app.business-services.edit', compact('workspace', 'businessService'));
    }

    public function update(UpdateBusinessServiceRequest $request, Workspace $workspace, BusinessService $businessService)
    {
        $this->ensureServiceBelongsToWorkspace($workspace, $businessService);

        $this->businessServiceService->update(
            service: $businessService,
            data: $request->validated()
        );

        return redirect()
            ->route('app.services.index', $workspace)
            ->with('success', 'تم تحديث الخدمة بنجاح.');
    }

    public function destroy(Workspace $workspace, BusinessService $businessService)
    {
        $this->ensureServiceBelongsToWorkspace($workspace, $businessService);

        $this->businessServiceService->delete($businessService);

        return redirect()
            ->route('app.services.index', $workspace)
            ->with('success', 'تم حذف الخدمة بنجاح.');
    }

    private function ensureServiceBelongsToWorkspace(Workspace $workspace, BusinessService $businessService): void
    {
        abort_if((int) $businessService->workspace_id !== (int) $workspace->id, 404);
    }
}