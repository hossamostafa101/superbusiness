<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\UserPlanPurchase;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * GET /api/profile
     * جلب بيانات البروفايل + إحصائيات بسيطة
     */
    public function show(Request $request)
    {
        $user = $request->user();

        // بيانات المستخدم الأساسية
        $userData = [
            'id'         => $user->id,
            'name'       => $user->name,
            'username'   => $user->username ?? null,
            'email'      => $user->email,
            'phone'      => $user->phone ?? null,
            'created_at' => $user->created_at ? $user->created_at->toDateTimeString() : null,
        ];

        // إحصائيات الدعوات (events)
        $eventsTotal = Invitation::where('user_id', $user->id)->count();
        $eventsPublished = Invitation::where('user_id', $user->id)
            ->where('is_published', true)
            ->count();

        // الرصيد من الباقات
        $purchasesQuery = UserPlanPurchase::where('user_id', $user->id)
            ->where('status', 'paid');

        $invitesPurchasedTotal = (int) (clone $purchasesQuery)->sum('invitations_total');
        $invitesAvailable      = (int) (clone $purchasesQuery)->sum('invitations_remaining');
        $invitesUsedTotal      = max($invitesPurchasedTotal - $invitesAvailable, 0);

        $lastPurchaseCurrency = (clone $purchasesQuery)->latest('id')->value('currency');
        $currency = $lastPurchaseCurrency ?: 'SAR';

        $stats = [
            'events_total'        => $eventsTotal,
            'events_published'    => $eventsPublished,
            'invites_available'   => $invitesAvailable,
            'invites_purchased_total' => $invitesPurchasedTotal,
            'invites_used_total'  => $invitesUsedTotal,
            'currency'            => $currency,
        ];

        return response()->json([
            'status'  => 'success',
            'message' => 'Profile loaded successfully',
            'data'    => [
                'user'  => $userData,
                'stats' => $stats,
            ],
        ]);
    }

    /**
     * PUT/PATCH /api/profile
     * تحديث بيانات البروفايل (name, username, email, phone)
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'     => ['sometimes', 'required', 'string', 'max:191'],
            'username' => ['sometimes', 'required', 'string', 'max:191', 'unique:users,username,' . $user->id],
            'email'    => ['sometimes', 'required', 'email', 'max:191', 'unique:users,email,' . $user->id],
            'phone'    => ['sometimes', 'nullable', 'string', 'max:20'],
            // لو حاب تضيف حقول ثانية (country, language, etc.) زوّدها هنا
        ]);

        $user->fill($validated);
        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Profile updated successfully',
            'data'    => [
                'user' => [
                    'id'       => $user->id,
                    'name'     => $user->name,
                    'username' => $user->username ?? null,
                    'email'    => $user->email,
                    'phone'    => $user->phone ?? null,
                ],
            ],
        ]);
    }
}
