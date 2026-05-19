<?php
// app/Http/Middleware/EnsureRestaurantAccount.php

namespace App\Http\Middleware;

use Closure;

class EnsureRestaurantAccount
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isRestaurantAccount()) {
            return redirect()->route('restaurant.login');
        }
        return $next($request);
    }
}
