<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('admin')->user();

        if (! $user || ! $user->isActive() || ! $user->isAdminUser()) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}