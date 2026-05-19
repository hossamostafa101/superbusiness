<?php

namespace App\Http\Middleware;

use App\Models\Workspace;
use Closure;
use Illuminate\Http\Request;

class EnsureWorkspaceAccess
{
    public function handle(Request $request, Closure $next)
    {
        $workspace = $request->route('workspace');

        if (! $workspace instanceof Workspace) {
            abort(404);
        }

        $user = auth('web')->user();

        if (! $user) {
            abort(403);
        }

        $hasAccess = $workspace->owner_id === $user->id
            || $workspace->users()->where('users.id', $user->id)->exists();

        if (! $hasAccess) {
            abort(403, 'ليس لديك صلاحية الوصول لهذه المساحة.');
        }

        return $next($request);
    }
}