<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {
        $this->middleware('admin.permission:users.view')->only(['index']);
$this->middleware('admin.permission:users.create')->only(['create', 'store']);
$this->middleware('admin.permission:users.edit')->only(['edit', 'update', 'toggleStatus']);
$this->middleware('admin.permission:users.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $users = User::query()
            ->with(['roles:id,name,guard_name', 'adminProfile'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->string('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.sections.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::query()
            ->where('guard_name', 'admin')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.sections.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->create($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح.');
    }

    public function edit(User $user)
    {
        $user->load(['roles:id,name,guard_name', 'adminProfile']);

        $roles = Role::query()
            ->where('guard_name', 'admin')
            ->orderBy('name')
            ->get(['id', 'name']);

        $selectedRoles = $user->roles()
            ->where('guard_name', 'admin')
            ->pluck('id')
            ->toArray();

        return view('admin.sections.users.edit', compact('user', 'roles', 'selectedRoles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->update($user, $request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'تم تحديث المستخدم بنجاح.');
    }

    public function destroy(User $user)
    {
        if (auth('admin')->id() === $user->id) {
            return back()->with('error', 'لا يمكنك حذف حسابك الحالي.');
        }

        if ($user->hasRole('super_admin', 'admin')) {
            $superAdminsCount = User::role('super_admin', 'admin')->count();

            if ($superAdminsCount <= 1) {
                return back()->with('error', 'لا يمكن حذف آخر Super Admin.');
            }
        }

        $this->userService->delete($user);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح.');
    }

    public function toggleStatus(User $user)
    {
        if (auth('admin')->id() === $user->id) {
            return back()->with('error', 'لا يمكنك إيقاف حسابك الحالي.');
        }

        if ($user->hasRole('super_admin', 'admin')) {
            $superAdminsCount = User::role('super_admin', 'admin')
                ->where('status', 'active')
                ->count();

            if ($user->status === 'active' && $superAdminsCount <= 1) {
                return back()->with('error', 'لا يمكن إيقاف آخر Super Admin نشط.');
            }
        }

        $this->userService->toggleStatus($user);

        return back()->with('success', 'تم تغيير حالة المستخدم بنجاح.');
    }
}