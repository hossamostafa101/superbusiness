<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsRestaurant
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // if ((int)($user->is_admin ?? 0) === 1) abort(403);
        // لو مش مسجل دخول
        if (!$user) {
              return redirect('/restaurant/login');
            // return redirect()->route('student.login');
        }

        // لازم يكون مرتبط بصف الطلاب
        if ($user->type !== 'restaurant') {
            abort(403, 'Restaurant access only.');
        }

        return $next($request);
    }
}