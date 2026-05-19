<?php

// app/Http/Controllers/Admin/PermissionController.php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index() {
        $permissions = Permission::orderBy('name')->paginate(30);
        return view('admin.sections.permissions.index', compact('permissions'));
    }

    public function create() { return view('admin.sections.permissions.create'); }

    public function store(Request $request) {
        $data = $request->validate(['name'=>'required|string|max:100|unique:permissions,name']);
        Permission::create($data);
        return redirect()->route('admin.permissions.index')->with('ok','تمت إضافة الصلاحية');
    }

    public function destroy(Permission $permission) {
        $permission->delete();
        return back()->with('ok','تم الحذف');
    }
}
