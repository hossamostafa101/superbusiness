<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWorkspaceRequest;
use App\Http\Requests\Admin\UpdateWorkspaceRequest;
use App\Models\User;
use App\Models\Workspace;
use App\Services\Admin\WorkspaceService;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function __construct(
        private readonly WorkspaceService $workspaceService
    ) {
            $this->middleware('admin.permission:workspaces.view')->only(['index']);
$this->middleware('admin.permission:workspaces.create')->only(['create', 'store']);
$this->middleware('admin.permission:workspaces.edit')->only(['edit', 'update', 'toggleStatus']);
$this->middleware('admin.permission:workspaces.delete')->only(['destroy']);
        
    }

    public function index(Request $request)
    {
        $workspaces = Workspace::query()
            ->with([
                'owner:id,name,email,phone,status',
                'activeSubscription.plan:id,name,slug,monthly_price,yearly_price,currency',
            ])
            ->withCount('users')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                            $ownerQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $types = Workspace::query()
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        return view('admin.sections.workspaces.index', compact('workspaces', 'types'));
    }

    public function create()
    {
        $owners = User::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        return view('admin.sections.workspaces.create', compact('owners'));
    }

    public function store(StoreWorkspaceRequest $request)
    {
        $this->workspaceService->create($request->validated());

        return redirect()
            ->route('admin.workspaces.index')
            ->with('success', 'تم إنشاء مساحة العمل بنجاح.');
    }

   public function edit(Workspace $workspace)
{
    $workspace->load('owner');

    $owners = User::query()
        ->where(function ($query) use ($workspace) {
            $query->where('status', 'active')
                ->orWhere('id', $workspace->owner_id);
        })
        ->orderBy('name')
        ->get(['id', 'name', 'email', 'phone']);

    return view('admin.sections.workspaces.edit', compact('workspace', 'owners'));
}

    public function update(UpdateWorkspaceRequest $request, Workspace $workspace)
    {
        $this->workspaceService->update($workspace, $request->validated());

        return redirect()
            ->route('admin.workspaces.index')
            ->with('success', 'تم تحديث مساحة العمل بنجاح.');
    }

    public function destroy(Workspace $workspace)
    {
        $this->workspaceService->delete($workspace);

        return redirect()
            ->route('admin.workspaces.index')
            ->with('success', 'تم حذف مساحة العمل بنجاح.');
    }

    public function toggleStatus(Workspace $workspace)
    {
        $this->workspaceService->toggleStatus($workspace);

        return back()->with('success', 'تم تغيير حالة مساحة العمل بنجاح.');
    }
}