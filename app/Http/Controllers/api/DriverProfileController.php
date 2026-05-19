<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\DriverProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DriverProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * GET /api/driver/profile
     */
    public function show(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user || $user->type !== 'driver') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        // لو مفيش profile اعمل واحد Default
        $profile = DriverProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'vehicle_type' => 'موتوسيكل',
                'plate_number' => null,
                'area'         => 'حدائق الأهرام',
                'is_active'    => 1,
            ]
        );

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'       => $user->id,
                'name'     => $user->name,
                'username' => $user->username,
                'email'    => $user->email,
                'phone'    => $user->phone,
                'profile'  => [
                    'vehicle_type' => $profile->vehicle_type,
                    'plate_number' => $profile->plate_number,
                    'area'         => $profile->area,
                    'is_active'    => (bool) $profile->is_active,
                    'photo_path'    => $profile->photo_path
                    ? Storage::disk('public')->url($profile->photo_path)
                    : null,
                ],
            ],
        ]);
    }


   public function update(Request $request)
{
    $user = Auth::guard('api')->user();

    if (!$user || $user->type !== 'driver') {
        return response()->json([
            'status'  => 'error',
            'message' => 'Unauthorized',
        ], 401);
    }

    // 👈 بس الاسم + الموبايل + الصورة
    $data = $request->validate([
        'name'   => ['sometimes', 'required', 'string', 'max:255'],
        'phone'  => ['sometimes', 'required', 'string', 'max:50'],

        // صورة السائق (اختياري)
        'avatar' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
    ]);

    // ✅ تحديث بيانات الـ user
    if (array_key_exists('name', $data)) {
        $user->name = $data['name'];
    }
    if (array_key_exists('phone', $data)) {
        $user->phone = $data['phone'];
    }
    $user->save();

    // ✅ تأكد إن عنده profile (من غير ما نغيّر vehicle_type / area / plate_number)
    $profile = DriverProfile::firstOrCreate(
        ['user_id' => $user->id],
        [
            'vehicle_type' => 'موتوسيكل',
            'plate_number' => null,
            'area'         => 'حدائق الأهرام',
            'is_active'    => 1,
            'status'       => 'pending',
        ]
    );

    $profileData = [];

    // ✅ صورة السائق فقط
    if ($request->hasFile('avatar')) {
        // لو عايز تحذف القديمة:
        // if ($profile->photo_path && Storage::disk('public')->exists($profile->photo_path)) {
        //     Storage::disk('public')->delete($profile->photo_path);
        // }

        $avatarPath = $request->file('avatar')->store('driver_avatars', 'public');
        $profileData['photo_path'] = 'app/public/'.$avatarPath; // تأكد إن عندك العمود ده في driver_profiles
    }

    if (!empty($profileData)) {
        $profile->update($profileData);
        $profile->refresh();
    }

    return response()->json([
        'status'  => 'success',
        'message' => 'تم تحديث البيانات بنجاح.',
        'data'    => [
            'id'       => $user->id,
            'name'     => $user->name,
            'username' => $user->username,
            'email'    => $user->email,
            'phone'    => $user->phone,
            'profile'  => [
                'vehicle_type' => $profile->vehicle_type,
                'plate_number' => $profile->plate_number,
                'area'         => $profile->area,
                'is_active'    => (bool) $profile->is_active,
                'photo_url'    => $profile->photo_path
                    ? Storage::disk('public')->url($profile->photo_path)
                    : null,
            ],
        ],
    ]);
}
}
