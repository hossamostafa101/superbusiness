<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\StoreBusinessLinkRequest;
use App\Http\Requests\App\UpdateBusinessLinkRequest;
use App\Models\BusinessLink;
use App\Models\Workspace;
use App\Services\App\BusinessLinkService;
use App\Services\Core\FeatureLimitService;
use Illuminate\Http\Request;

class BusinessLinkController extends Controller
{
    public function __construct(
        private readonly BusinessLinkService $businessLinkService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function index(Workspace $workspace)
    {
        $links = $workspace->businessLinks()
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(15);

        $linksLimit = $this->featureLimitService->limit($workspace, 'links_limit', 3);
        $isUnlimited = $linksLimit === -1;

        return view('app.business-links.index', compact(
            'workspace',
            'links',
            'linksLimit',
            'isUnlimited'
        ));
    }

    public function create(Workspace $workspace)
    {
        $currentCount = $workspace->businessLinks()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'links_limit', $currentCount)) {
            return redirect()
                ->route('app.links.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى للروابط في باقتك الحالية.');
        }

        return view('app.business-links.create', compact('workspace'));
    }

    public function store(StoreBusinessLinkRequest $request, Workspace $workspace)
    {
        $currentCount = $workspace->businessLinks()->count();

        if (! $this->featureLimitService->canCreate($workspace, 'links_limit', $currentCount)) {
            return redirect()
                ->route('app.links.index', $workspace)
                ->with('error', 'وصلت للحد الأقصى للروابط في باقتك الحالية.');
        }

        $this->businessLinkService->create(
            workspace: $workspace,
            data: $request->validated()
        );

        return redirect()
            ->route('app.links.index', $workspace)
            ->with('success', 'تم إضافة الرابط بنجاح.');
    }

    public function edit(Workspace $workspace, BusinessLink $businessLink)
    {
        $this->ensureLinkBelongsToWorkspace($workspace, $businessLink);

        return view('app.business-links.edit', compact('workspace', 'businessLink'));
    }

    public function update(UpdateBusinessLinkRequest $request, Workspace $workspace, BusinessLink $businessLink)
    {
        $this->ensureLinkBelongsToWorkspace($workspace, $businessLink);

        $this->businessLinkService->update(
            businessLink: $businessLink,
            data: $request->validated()
        );

        return redirect()
            ->route('app.links.index', $workspace)
            ->with('success', 'تم تحديث الرابط بنجاح.');
    }

    public function destroy(Workspace $workspace, BusinessLink $businessLink)
    {
        $this->ensureLinkBelongsToWorkspace($workspace, $businessLink);

        $this->businessLinkService->delete($businessLink);

        return redirect()
            ->route('app.links.index', $workspace)
            ->with('success', 'تم حذف الرابط بنجاح.');
    }

    private function ensureLinkBelongsToWorkspace(Workspace $workspace, BusinessLink $businessLink): void
    {
        abort_if((int) $businessLink->workspace_id !== (int) $workspace->id, 404);
    }
}