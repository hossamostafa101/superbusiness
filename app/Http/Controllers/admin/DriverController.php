<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\DriverProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    public function index()
    {
        // list only users of type=driver with profile

        $drivers = DriverProfile::with('user')
            // ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.sections.drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('admin.sections.drivers.create');
    }
    public function store(Request $request)
    {
        // validate both user + driver data
        $data = $request->validate([
            // user fields
            'name'          => 'required|string|max:255',
            'username'      => 'required|string|max:255|unique:users,username',
            'email'         => 'required|email|max:255|unique:users,email',
            'phone'         => 'nullable|string|max:255',
            'password'      => 'required|string|min:6|confirmed', // needs password_confirmation

            // driver_profile fields
            'vehicle_type'  => 'nullable|string|max:100',
            'plate_number'  => 'nullable|string|max:50',
            'area'          => 'nullable|string|max:100',
            'is_active'     => 'nullable|boolean',

            // صورة السائق
            'avatar'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        DB::transaction(function () use ($data, $request) {
            // 1) create user
            $user = User::create([
                'name'      => $data['name'],
                'username'  => $data['username'],
                'email'     => $data['email'],
                'phone'     => $data['phone'] ?? null,
                'password'  => Hash::make($data['password']),
                'type'      => 'driver',
                'is_admin'  => 0,
            ]);

            // 2) رفع الصورة (لو موجودة)
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('driver_avatars', 'public');
            }

            // 3) create driver profile
            DriverProfile::create([
                'user_id'      => $user->id,
                'vehicle_type' => $data['vehicle_type'],
                'plate_number' => $data['plate_number'],
                'area'         => $data['area'],
                'is_active'    => $data['is_active'] ?? 1,
                'status'       => 'approved',      // من لوحة التحكم → مفعل
                'photo_path'   => $avatarPath,
            ]);
        });

        return redirect()
            ->route('admin.drivers.index')
            ->with('success', 'تم إنشاء السائق بنجاح.');
    }


    public function edit(User $driver)
    {
        // route model binding: admin/drivers/{driver}
        // make sure Route::resource uses User as model or change type-hint
        $driver->load('driverProfile');

        return view('admin.sections.drivers.edit', compact('driver'));
    }
    public function update(Request $request, User $driver)
    {
        try{
        $driver->load('driverProfile');

        $data = $request->validate([
            // user fields
            'name'          => 'required|string|max:255',
            'username'      => 'required|string|max:255|unique:users,username,' . $driver->id,
            'email'         => 'required|email|max:255|unique:users,email,' . $driver->id,
            'phone'         => 'nullable|string|max:255',
            'password'      => 'nullable|string|min:6|confirmed',

            // driver_profile fields
            'vehicle_type'  => 'required|string|max:100',
            'plate_number'  => 'required|string|max:50',
            'area'          => 'required|string|max:100',
            'is_active'     => 'nullable|boolean',

            // صورة جديدة (اختياري)
            'avatar'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        DB::transaction(function () use ($data, $driver, $request) {
            // 1) update user
            $driver->name     = $data['name'];
            $driver->username = $data['username'];
            $driver->email    = $data['email'];
            $driver->phone    = $data['phone'] ?? null;

            if (!empty($data['password'])) {
                $driver->password = Hash::make($data['password']);
            }

            $driver->type = 'driver';
            $driver->save();

            // 2) بيانات البروفايل
            $profileData = [
                'vehicle_type' => $data['vehicle_type'],
                'plate_number' => $data['plate_number'],
                'area'         => $data['area'],
                'is_active'    => $data['is_active'] ?? 1,
            ];

            // 3) لو فيه صورة جديدة
            if ($request->hasFile('avatar')) {
                $newPath = $request->file('avatar')->store('driver_avatars', 'public');

                // حذف القديمة (لو موجودة)
                if ($driver->driverProfile && $driver->driverProfile->photo_path) {
                    Storage::disk('public')->delete($driver->driverProfile->photo_path);
                }

                $profileData['photo_path'] = $newPath;
            }

            if ($driver->driverProfile) {
                $driver->driverProfile->update($profileData);
            } else {
                $driver->driverProfile()->create($profileData + [
                    'status' => 'approved',
                ]);
            }
        });

        return redirect()
            ->route('admin.drivers.index')
            ->with('success', 'تم تحديث بيانات السائق بنجاح.');
    }catch(\Exception $e){
        return response()->json(['error' => $e->getMessage()], 500);
        return redirect()
            ->route('admin.drivers.index')
            ->with('error', 'حدث خطأ أثناء تحديث بيانات السائق: '.$e->getMessage());
    }

    }


    public function destroy(User $driver)
    {
        DB::transaction(function () use ($driver) {
            // delete profile first
            $driver->driverProfile()->delete();
            // then user
            $driver->delete();
        });

        return redirect()
            ->route('admin.drivers.index')
            ->with('success', 'Driver deleted successfully.');
    }



    public function requestLocation(DriverProfile $driver)
    {
        $user = $driver->user;               // علاقة user() في DriverProfile
        $token = $user->fcm_token ?? null;   // أنت أصلاً ضفت fcm_token في users

        if (! $token) {
            return back()->with('error', 'لا يوجد FCM token لهذا السائق.');
        }

        $accessToken = app(AdminNotifyController::class)->getAccessToken();
        // أو استعمل نفس الخدمة اللي بتجيب توكن FCM v1

        $projectId = 'quickmart-fbc37'; // زي ما عندك في كود الإشعارات

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $token,
                'data'  => [
                    'action' => 'request_driver_location',
                    "user_id" => (string) $user->id,
                    "reason"   => "طلب موقع السائق من لوحة متابعة الطلبات",
                ],
            ],
        ];

        $response = Http::withToken($accessToken)->post($url, $payload);

        if (! $response->successful()) {
            return back()->with('error', 'فشل إرسال طلب الموقع: ' . $response->body());
        }

        return back()->with('success', 'تم إرسال طلب الموقع للسائق، سيتم تحديث الموقع عند استجابته.');
    }

    // handle the driver activity status toggle
    public function toggleActiveStatus(DriverProfile $driver)
    {
        $driver->is_active = $driver->is_active ? 0 : 1;
        $driver->save();
    

        return redirect()
            ->route('admin.drivers.index')
            ->with('success', 'تم تحديث حالة نشاط السائق بنجاح.');
    }   
}

