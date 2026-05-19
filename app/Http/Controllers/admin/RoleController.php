<?php

// app/Http/Controllers/Admin/RoleController.php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index() {
        $roles = Role::withCount('permissions')->paginate(15);
        return view('admin.sections.roles.index', compact('roles'));
    }

    public function create() {
        $permissions = Permission::orderBy('name')->get()->groupBy(fn($p)=>explode('.',$p->name)[0]); // تجميع حسب الموديول
        return view('admin.sections.roles.create', compact('permissions'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'permissions' => 'array'
        ]);
        $role = Role::create(['name'=>$data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);
        return redirect()->route('admin.roles.index')->with('ok','تم إنشاء الدور');
    }

    public function edit(Role $role) {
        $permissions = Permission::orderBy('name')->get()->groupBy(fn($p)=>explode('.',$p->name)[0]);
        $rolePerms = $role->permissions->pluck('name')->toArray();
        return view('admin.sections.roles.edit', compact('role','permissions','rolePerms'));
    }

    public function update(Request $request, Role $role) {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,'.$role->id,
            'permissions' => 'array'
        ]);
        $role->update(['name'=>$data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);
        return redirect()->route('admin.roles.index')->with('ok','تم تحديث الدور');
    }

    public function destroy(Role $role) {
        if ($role->name === 'admin') return back()->with('err','لا يمكن حذف دور admin');
        $role->delete();
        return back()->with('ok','تم الحذف');
    }
}
