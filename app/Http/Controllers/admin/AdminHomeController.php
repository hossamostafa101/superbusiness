<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminHomeController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'admin_roles' => Role::where('guard_name', 'admin')->count(),
            'admin_permissions' => Permission::where('guard_name', 'admin')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}