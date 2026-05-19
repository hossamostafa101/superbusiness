<?php

namespace App\Http\Controllers;

use App\Models\DriverProfile;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function __construct()
    {
        // فقط register / login / refresh بدون توكن
        $this->middleware('auth:api', [
            'except' => ['login', 'register', 'refresh', 'vendorRegister', 'vendorLogin', 'driverRegister', 'driverLogin', 'registerVendor', 'registerDriver'],
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'type'     => 1, // مثلاً 1 = vendor (عدّلها حسب مشروعك)
        ]);

        $token = Auth::guard('api')->login($user);

        return response()->json([
            'status'  => 'success',
            'message' => 'User created successfully',
            'user'    => $user,
            'authorisation' => [
                'token' => $token,
                'type'  => 'bearer',
            ],
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            // بيانات دخول غلط
            return response()->json([
                'status'  => false,
                'pending' => false,
                'message' => 'بيانات الدخول غير صحيحة.',
            ], 200); // خليك على 200 علشان الموبايل يقدر يقرأ الرسالة
        }

        /** @var \App\Models\User $user */
        $user = Auth::guard('api')->user();

        // لو عايز الـ endpoint ده خاص بالـ Vendor بس
        // if ($user->type !== 'vendor') {
        //     Auth::guard('api')->logout();

        //     return response()->json([
        //         'status'  => false,
        //         'pending' => false,
        //         'message' => 'هذا الحساب غير مسموح له بالدخول من هذا التطبيق.',
        //     ], 200);
        // }

        // جِب الـ VendorProfile المرتبط بالمستخدم
        // $vendorProfile = $user->vendorProfile; // مهم يكون فيه relation في User model

        // if (! $vendorProfile) {
        //     Auth::guard('api')->logout();

        //     return response()->json([
        //         'status'  => false,
        //         'pending' => false,
        //         'message' => 'لم يتم العثور على ملف المورد المرتبط بهذا الحساب.',
        //     ], 200);
        // }

        // لو لسه الأدمن ما وافقش (status = pending أو أي حالة غير approved)
        // if ($vendorProfile->status !== 'approved') {
        //     Auth::guard('api')->logout();

        //     return response()->json([
        //         'status'  => false,
        //         'pending' => true, // ✅ الموبايل هيعتمد عليها
        //         'message' => 'حسابك قيد المراجعة من الإدارة، برجاء المحاولة لاحقاً.',
        //     ], 200);
        // }

        // هنا كل شيء تمام → Vendor متقبل
        return response()->json([
            'status'  => true,
            'pending' => false,
            'message' => 'تم تسجيل الدخول بنجاح.',
            'token'   => $token,                         // 👈 الـ Flutter بيقرأ من هنا
            'user'    => $user->load('vendorProfile'),   // لو حابب تبعت البروفايل معاه
            'authorisation' => [                         // لو عندك clients قديمة بتستخدمه
                'token' => $token,
                'type'  => 'bearer',
            ],
        ], 200);
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'status'  => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user'   => Auth::guard('api')->user(),
            'authorisation' => [
                'token' => Auth::guard('api')->refresh(),
                'type'  => 'bearer',
            ],
        ]);
    }

    public function me()
    {
        return response()->json([
            'status' => 'success',
            'user'   => Auth::guard('api')->user(),
        ]);
    }

    // مثال لتعديل الاسم والموبايل فقط
    public function updateNameAndPhone(Request $request)
    {
        $user = Auth::guard('api')->user();

        $request->validate([
            'name'  => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|nullable|string|max:255',
        ]);

        $user->update($request->only('name', 'phone'));

        return response()->json([
            'status'  => 'success',
            'message' => 'Profile updated successfully',
            'user'    => $user,
        ]);
    }










    public function vendorRegister(Request $request)
    {

        $data = $request->validate([
            'owner_name'     => 'required|string|max:255',
            'business_name'  => 'required|string|max:255',
            'phone'          => 'required|string|max:255',
            'email'          => 'required|string|email|max:255|unique:users,email',
            'password'       => 'required|string|min:6',

            'pickup_address' => 'required|string|max:255',
            'lat'            => 'nullable|numeric',
            'lng'            => 'nullable|numeric',
            'tour_number'    => 'required|string|max:50',
            'area_letter'    => 'required|string|max:5',
            'notes'          => 'nullable|string',
            'gate'           => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();

        try {

            //is user registed new user
            if (User::where('email', $data['email'])->exists()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Email already registered',
                ], 400);
            }
            // user = صاحب النشاط
            $user = User::create([
                'name'     => $data['owner_name'],
                'username' => $data['email'], // أو اعمل username منفصل لو حابب
                'email'    => $data['email'],
                'phone'    => $data['phone'],
                'password' => Hash::make($data['password']),
                'type'     => 'vendor',
                'title'    => $data['business_name'],
                'is_admin' => 0,
            ]);

            // vendor profile = بيانات المحل
            $vendor = VendorProfile::create([
                'user_id'         => $user->id,
                'owner_name'      => $data['owner_name'],
                'business_name'            => $data['business_name'],
                'pickup_address'  => $data['pickup_address'],
                'gate'            => $data['gate'] ?? $data['tour_number'], // اختار اللي يناسبك
                'region_letter'   => $data['area_letter'],
                'building_number' => $data['tour_number'],
                'notes'           => $data['notes'] ?? null,
                'lat'             => $data['lat'] ?? null,
                'lng'             => $data['lng'] ?? null,
            ]);

            $token = Auth::guard('api')->login($user);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Vendor created successfully',
                'user' => $user->load('vendor'),
                'authorisation' => [
                    'token' => $token,
                    'type'  => 'bearer',
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Vendor registration failed',
                'error'   => $e->getMessage() . $e->getLine(),
            ], 500);
        }
    }



    public function driverRegister(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users,email',
            'phone'        => 'required|string|max:255',
            'password'     => 'required|string|min:6',

            'vehicle_type' => 'nullable|string|max:100',
            // 'plate_number' => 'nullable|string|max:50',
            // 'area'         => 'nullable|string|max:100',
        ]);

        // return response()->json([
        //         'status'  => 'error',
        //         'message' => 'Vendor registration failed',
        //     ], 401);
        DB::beginTransaction();

        try {
                if (User::where('email', $data['email'])->exists()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Email already registered',
                ], 400);
            }
            $user = User::create([
                'name'     => $data['name'],
                'username' => $data['email'],
                'email'    => $data['email'],
                'phone'    => $data['phone'],
                'password' => Hash::make($data['password']),
                'type'     => 'driver',
                'title'    => 'Delivery Driver',
                'is_admin' => 0,
            ]);

            $profile = DriverProfile::create([
                'user_id'      => $user->id,
                'vehicle_type' => $data['vehicle_type'],
                'plate_number' => $data['plate_number']??'0',
                'area'         => $data['area'] ?? 'حدائق الأهرام',
                'is_active'    => 1,
            ]);

            $token = Auth::guard('api')->login($user);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Driver created successfully',
                'user' => $user->load('driverProfile'),
                'authorisation' => [
                    'token' => $token,
                    'type'  => 'bearer',
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Driver registration failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function vendorLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::guard('api')->user();

        if ($user->type !== 'vendor') {
            Auth::guard('api')->logout();
            return response()->json([
                'status'  => 'error',
                'message' => 'This account is not a vendor',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'user'   => $user->load('vendor'),
            'authorisation' => [
                'token' => $token,
                'type'  => 'bearer',
            ],
        ]);
    }

    public function driverLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'status'  => false,
                'pending' => false,
                'message' => 'بيانات الدخول غير صحيحة.',
            ], 200);
        }

        /** @var User $user */
        $user = Auth::guard('api')->user();

        if ($user->type !== 'driver') {
            return response()->json([
                'status'  => false,
                'pending' => false,
                'message' => 'هذا الحساب ليس من نوع سائق.',
            ], 200);
        }

        $profile = $user->driverProfile;

        if (! $profile) {
            return response()->json([
                'status'  => false,
                'pending' => false,
                'message' => 'لا يوجد ملف سائق مرتبط بهذا المستخدم.',
            ], 200);
        }

        if ($profile->status !== 'approved') {
            // نمنع الدخول ونرجع pending = true
            return response()->json([
                'status'  => false,
                'pending' => true,
                'message' => 'حسابك كسائق ما زال قيد المراجعة من الإدارة.',
            ], 200);
        }

        // كل شيء تمام → نرجع توكن وبيانات السائق
        return response()->json([
            'status'  => true,
            'pending' => false,
            'message' => 'تم تسجيل الدخول بنجاح.',
            'token'   => $token,
            'driver'  => [
                'user'    => $user,
                'profile' => $profile,
            ],
        ], 200);
    }

    public function registerVendor(Request $request)
    {

        $data = $request->validate([
            'name'           => 'required|string|max:255', // owner name
            'business_name'  => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'phone'          => 'required|string|max:255',
            'password'       => 'required|string|min:6',

            'pickup_address' => 'required|string|max:255',
            'lat'            => 'nullable|numeric',
            'lng'            => 'nullable|numeric',
            // 'gate'           => 'nullable|string|max:50',
            'region_letter'  => 'nullable|string|max:50',
            'building_number' => 'nullable|string|max:50',
            // 'notes'          => 'nullable|string',
    // 'section'         => 'nullable|string|max:50', 
    
        'logo'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        //   return response()->json([
        //             'status'  => 'error',
        //             'message' => 'Use /auth/vendor/register endpoint instead.',
        //         ], 180);

        try{
        //is user registed new user
        if (User::where('email', $data['email'])->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email already registered',
            ], 400);
        }

        $user = User::create([
            'name'     => $data['name'],
            'username' => $data['email'], // أو رقم التليفون لو حابب
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
            'type'     => 'vendor',
            'title'    => 'vendor',
        ]);

         $logoPath = null;
    if ($request->hasFile('logo')) {
        $logoPath = $request->file('logo')->store('vendor_logos', 'public');
    }

        $vendor = VendorProfile::create([
            'user_id'        => $user->id,
            'owner_name'     => $data['name'],
            'business_name'  => $data['business_name'],
            'logo_path'      => $logoPath,  
            'pickup_address' => $data['pickup_address'],
            'lat'            => $data['lat'] ?? null,
            'lng'            => $data['lng'] ?? null,
            // 'gate'           => $data['gate'] ?? null,
            'region_letter'  => $data['region_letter'] ?? null,
            'building_number' => $data['building_number'] ?? null,
            // 'notes'          => $data['notes'] ?? null,
            'status'         => 'pending',
    // 'section'        => $data['section'] ?? null, 
        ]);

        // اعمل JWT token حسب الباكج اللي عندك
        $token = auth('api')->login($user);

        return response()->json([
            'status' => 'success',
            'user'   => $user,
            'vendor' => $vendor,
            'token'  => $token,
        ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Vendor registration failed',
                'error'   => $e->getMessage() . $e->getLine(),
            ], 500);
        }
    }

    public function registerDriver(Request $request)
     {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'phone'    => 'required|string|max:50',
            'password' => 'required|string|min:6',

            'vehicle_type' => 'nullable|string|max:100',
            'plate_number' => 'nullable|string|max:50',
            'area'         => 'nullable|string|max:100',
            
            'avatar'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

        ]);

        // لو فيه user بنفس الإيميل أو الموبايل
        $existingUser = User::where('email', $data['email'])
            ->orWhere('phone', $data['phone'])
            ->first();

        if ($existingUser && $existingUser->type === 'driver') {
            $profile = $existingUser->driverProfile;

            if ($profile && $profile->status === 'pending') {
                return response()->json([
                    'status'  => false,
                    'pending' => true,
                    'message' => 'لديك طلب تسجيل سائق قيد المراجعة، سيتم التواصل معك بعد موافقة الإدارة.',
                ], 200);
            }

            if ($profile && $profile->status === 'approved') {
                return response()->json([
                    'status'  => false,
                    'pending' => false,
                    'message' => 'لديك حساب سائق مفعل بالفعل، الرجاء تسجيل الدخول.',
                ], 200);
            }

            if ($profile && $profile->status === 'rejected') {
                return response()->json([
                    'status'  => false,
                    'pending' => false,
                    'message' => 'تم رفض طلبك سابقًا، الرجاء التواصل مع الإدارة.',
                ], 200);
            }
        }

        // إنشاء user جديد من نوع driver
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
            'type'     => 'driver', // تأكد إن عندك عمود type في users
            'username' => strtok($data['email'], '@'),
        ]);

        $avatarPath = null;
if ($request->hasFile('avatar')) {
    $avatarPath = $request->file('avatar')->store('driver_avatars', 'public');
}

        // إنشاء ملف السائق بحالة pending
        $profile = DriverProfile::create([
            'user_id'      => $user->id,
            'vehicle_type' => $data['vehicle_type'] ?? null,
            'plate_number' => $data['plate_number'] ?? null,
            'area'         => $data['area'] ?? 'حدائق الأهرام',
            'is_active'    => 1,
            'status'       => 'pending',
            'photo_path'   => 'app/public/'.$avatarPath, 
        ]);

        return response()->json([
            'status'  => true,
            'pending' => true,
            'message' => 'تم استلام طلب التسجيل كسائق بنجاح، سيتم التواصل معك بعد مراجعة الطلب من الإدارة.',
            // لا نرجع توكن هنا لأنه لسه مش approved
        ], 201);
    }

    public function loginVendor(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                'status'  => false,
                'pending' => false,
                'message' => 'بيانات الدخول غير صحيحة.',
            ], 200);
        }

        /** @var \App\Models\User $user */
        $user = auth('api')->user();

        // لو مش Vendor أصلاً
        if ($user->type !== 'vendor') {
            auth('api')->logout();
            return response()->json([
                'status'  => false,
                'pending' => false,
                'message' => 'هذا الحساب غير مسموح له بالدخول من هذا التطبيق.',
            ], 200);
        }

        $vendorProfile = $user->vendorProfile; // اعمل hasOne في User model

        if (! $vendorProfile || $vendorProfile->status !== 'approved') {
            auth('api')->logout(); // ما نبعتش token
            return response()->json([
                'status'  => false,
                'pending' => true, // ✅ علشان الموبايل يعرف إنها انتظار موافقة
                'message' => 'حسابك قيد المراجعة من الإدارة، برجاء المحاولة لاحقاً.',
            ], 200);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل الدخول بنجاح.',
            'token'  => $token,
            'data'   => $user->load('vendorProfile'),
        ]);
    }

     public function setFcmToken(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (! $user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $data = $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $user->fcm_token = $data['fcm_token'];
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'FCM token updated successfully.',
        ]);
    }
}
