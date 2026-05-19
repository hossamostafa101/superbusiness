<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureWorkspaceSpecification
{
    public function handle(Request $request, Closure $next, string $specification)
    {
        $workspace = $request->route('workspace');

        abort_if(! $workspace, 404);

        $workspace->loadMissing('specification');

        abort_if($workspace->specificationKey() !== $specification, 403, 'هذه المساحة لا تدعم هذا الموديول.');

        return $next($request);
    }
}