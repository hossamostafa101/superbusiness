<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetCurrentBranch
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // نشتغل فقط مع حسابات المطاعم
        if ($user && $user->isRestaurantAccount()) {

            if (! $request->session()->has('current_branch_id')) {

                $restaurant = $user->restaurants()->with('branches')->first();

                if ($restaurant) {
                    // لو اليوزر مربوط بفرع في الـ pivot نستخدمه
                    $pivot = $restaurant->users()
                        ->where('users.id', $user->id)
                        ->first()?->pivot;

                    if ($pivot && $pivot->branch_id) {
                        $request->session()->put('current_branch_id', $pivot->branch_id);
                    } else {
                        // وإلا استخدم الفرع الرئيسي ثم أول فرع متاح
                        $branch = $restaurant->branches()
                                ->where('is_main', true)
                                ->first()
                            ?? $restaurant->branches()->first();

                        if ($branch) {
                            $request->session()->put('current_branch_id', $branch->id);
                        }
                    }
                }

            } else {
                // تأكيد أن الفرع الموجود في السيشن ما زال يتبع هذا المطعم
                $branchId   = $request->session()->get('current_branch_id');
                $restaurant = $user->restaurants()->first();

                if ($restaurant) {
                    $exists = $restaurant->branches()
                        ->where('id', $branchId)
                        ->exists();

                    if (! $exists) {
                        $branch = $restaurant->branches()
                                ->where('is_main', true)
                                ->first()
                            ?? $restaurant->branches()->first();

                        if ($branch) {
                            $request->session()->put('current_branch_id', $branch->id);
                        } else {
                            $request->session()->forget('current_branch_id');
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
