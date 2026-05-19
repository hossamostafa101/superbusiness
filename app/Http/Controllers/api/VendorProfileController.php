<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\VendorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorProfileController extends Controller
{
    /**
     * رجوع بيانات اليوزر + vendor_profile
     * شكل الريسكونس:
     * {
     *   "status": true,
     *   "message": null,
     *   "data": { id, name, email, phone, type, title, vendor_profile: { ... } }
     * }
     */
    public function show(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user('api'); // أو Auth::guard('api')->user();

        if (!$user || $user->type !== 'vendor') {
            return response()->json([
                'status'  => false,
                'message' => 'Not a vendor user.',
                'data'    => null,
            ], 403);
        }

        // تحميل علاقة vendorProfile عشان تطلع في الـ JSON باسم vendor_profile
        $user->load('vendorProfile');

        return response()->json([
            'status'  => true,
            'message' => null,
            'data'    => $user,
        ]);
    }

    /**
     * تحديث بيانات اليوزر + vendor_profile
     * body مثال:
     * {
     *   "name": "صاحب النشاط",
     *   "phone": "0100...",
     *   "email": "x@y.com",
     *   "owner_name": "صاحب النشاط",
     *   "business_name": "اسم النشاط",
     *   "pickup_address": "حدائق الأهرام - ...",
     *   "lat": 29.9,
     *   "lng": 31.1,
     *   "gate": "بوابة 3",
     *   "region_letter": "أ",
     *   "building_number": "100",
     *   "tour_number": "3",
     *   "notes": "ملاحظات..."
     * }
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user('api');

        if (!$user || $user->type !== 'vendor') {
            return response()->json([
                'status'  => false,
                'message' => 'Not a vendor user.',
                'data'    => null,
            ], 403);
        }

        $validated = $request->validate([
            // حقول user
            'name'   => 'sometimes|string|max:255',
            'phone'  => 'sometimes|string|max:50',
            'email'  => 'sometimes|email|max:255',

            // حقول vendor_profiles
            'owner_name'      => 'sometimes|string|max:255',
            'business_name'   => 'sometimes|string|max:255',
            'pickup_address'  => 'sometimes|string|max:500',
            'lat'             => 'sometimes|numeric|nullable',
            'lng'             => 'sometimes|numeric|nullable',
            'gate'            => 'sometimes|string|max:50|nullable',
            'region_letter'   => 'sometimes|string|max:10|nullable',
            'building_number' => 'sometimes|string|max:50|nullable',
            'tour_number'     => 'sometimes|string|max:50|nullable',
            'notes'           => 'sometimes|string|nullable',
        ]);

        // فصل بيانات اليوزر عن بيانات البروفايل
        $userData = collect($validated)->only([
            'name', 'phone', 'email',
        ])->toArray();

        $profileData = collect($validated)->only([
            'owner_name',
            'business_name',
            'pickup_address',
            'lat',
            'lng',
            'gate',
            'region_letter',
            'building_number',
            'tour_number',
            'notes',
        ])->toArray();

        // تحديث user
        if (!empty($userData)) {
            $user->fill($userData);
            $user->save();
        }

        // تحديث أو إنشاء VendorProfile
        $profile = $user->vendorProfile;

        if (!$profile) {
            $profile = new VendorProfile();
            $profile->user_id = $user->id;
        }

        if (!empty($profileData)) {
            $profile->fill($profileData);
            $profile->save();
        }

        $user->load('vendorProfile');

        return response()->json([
            'status'  => true,
            'message' => 'Profile updated successfully.',
            'data'    => $user,
        ]);
    }
}
