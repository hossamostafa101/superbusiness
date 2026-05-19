<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = auth('admin')->user();

        if (! $user) {
            abort(403, 'Unauthorized.');
        }

        $permissions = $user->getAllPermissions()
            ->where('guard_name', 'admin')
            ->pluck('name')
            ->toArray();

        if (! in_array($permission, $permissions, true)) {
            abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS.');
        }

        return $next($request);
    }
}