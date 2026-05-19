<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('admin')->user();

        if (! $user || ! $user->isActive() || ! $user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}