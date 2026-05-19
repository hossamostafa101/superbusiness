<?php
// app/Http/Middleware/SetCurrentRestaurant.php

namespace App\Http\Middleware;

use Closure;

class SetCurrentRestaurant
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        $currentId = session('current_restaurant_id');

        // لو في currentId تأكد إنه ضمن مطاعم المستخدم
        if ($currentId && $user->restaurants()->where('restaurants.id', $currentId)->exists()) {
            return $next($request);
        }

        // لو مش موجود: لو عنده مطعم واحد خليه تلقائي
        $first = $user->restaurants()->select('restaurants.id')->first();
        if ($first) {
            session(['current_restaurant_id' => $first->id]);
            return $next($request);
        }

        // لو مستخدم مطعم لكن بدون عضوية (حالة غلط) -> logout
        auth()->logout();
        return redirect()->route('restaurant.login')->withErrors(['email' => 'لا يوجد مطعم مرتبط بهذا الحساب.']);
    }
}
