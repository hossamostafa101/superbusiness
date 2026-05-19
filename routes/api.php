<?php

use App\Http\Controllers\api\ApiHomeController;
use App\Http\Controllers\api\DriverLocationController;
use App\Http\Controllers\api\DriverOrderController;
use App\Http\Controllers\api\DriverProfileController;
use App\Http\Controllers\api\EmojiNotifyController;
use App\Http\Controllers\api\GuestInvitationController;
use App\Http\Controllers\api\HomeController;
use App\Http\Controllers\api\InvitationController;
use App\Http\Controllers\api\InvitationGuestController;
use App\Http\Controllers\api\MyEventsController;
use App\Http\Controllers\api\PlanController;
use App\Http\Controllers\api\ProfileController;
use App\Http\Controllers\api\QrController;
use App\Http\Controllers\api\UserPlanPurchaseController;
use App\Http\Controllers\api\VendorOrderController;
use App\Http\Controllers\api\VendorProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Auth routes (JWT)
|--------------------------------------------------------------------------
| Public: register / login / refresh / logout
*/

Route::post('/emoji/notify', [EmojiNotifyController::class, 'notifyParent']);


Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
    Route::post('refresh',  [AuthController::class, 'refresh']);
    Route::post('logout',   [AuthController::class, 'logout']);
    Route::post('vendor/register', [AuthController::class, 'registerVendor']);
    Route::post('driver/register', [AuthController::class, 'registerDriver']);
    Route::post('login', [AuthController::class, 'login']);
});


// ========= VENDOR API =========
Route::prefix('vendor')->group(function () {
    Route::post('register', [AuthController::class, 'vendorRegister']);
    Route::post('login',    [AuthController::class, 'vendorLogin']);
});

// ========= DRIVER API =========
Route::prefix('driver')->group(function () {
    Route::post('register', [AuthController::class, 'registerDriver']);
    Route::post('login',    [AuthController::class, 'driverLogin']);
    Route::post('location/ping', [DriverLocationController::class, 'ping']);
    Route::post('location', [DriverLocationController::class, 'updateLocation']);


});

// ========= SHARED JWT ROUTES =========
Route::post('refresh', [AuthController::class, 'refresh']);
Route::post('logout',  [AuthController::class, 'logout']);
/*
|--------------------------------------------------------------------------
| Protected routes (need Authorization: Bearer <token>)
|--------------------------------------------------------------------------
*/
// api test
Route::get('test', function () {
    return response()->json([
        'status'  => 'success',
        'message' => 'API is working fine.',
    ]);
});







// عرض الدعوة بالـ slug للـ landing page (بدون Auth)
Route::get('/public/invitations/{slug}', [InvitationController::class, 'publicShow']);


// ===========================
// Public plans (للمستخدم العادي)
// ===========================
// Route::get('/plans', [PlanController::class, 'index']);
// Route::get('/plans/{id}', [PlanController::class, 'show']);




Route::middleware('auth:api')->group(function () {

    // ----- generic user info -----
    Route::get('me', [AuthController::class, 'me']);
    Route::put('me', [AuthController::class, 'update']); // optional generic update
    Route::post('me/name-phone', [AuthController::class, 'updateNameAndPhone']);

    // If you still want /user for compatibility:
    Route::get('/user', function (Request $request) {
        return $request->user();
    });






        // ============ Invitations (لصاحب الدعوة) ============

    // شاشة إضافة دعوة (ترجع templates + balance + defaults)
    Route::get('/invitations/add-page', [InvitationController::class, 'addPage']);

    // قائمة الدعوات
    Route::get('/invitations', [InvitationController::class, 'index']);

    // إنشاء دعوة جديدة
    Route::post('/invitations', [InvitationController::class, 'store']);

    // عرض دعوة واحدة
    Route::get('/invitations/{id}', [InvitationController::class, 'show']);

    // تحديث دعوة
    Route::put('/invitations/{id}', [InvitationController::class, 'update']);
    Route::patch('/invitations/{id}', [InvitationController::class, 'update']);

    // حذف دعوة
    Route::delete('/invitations/{id}', [InvitationController::class, 'destroy']);

    // نشر / إلغاء نشر
    Route::post('/invitations/{id}/publish', [InvitationController::class, 'publish']);
    Route::post('/invitations/{id}/unpublish', [InvitationController::class, 'unpublish']);


    // ============ Guests (إدارة المدعوين لصاحب الدعوة) ============

    // قائمة المدعوين لدعوة معيّنة
    Route::get('/invitations/{invitation}/guests', [InvitationGuestController::class, 'index']);

    // إضافة مدعو جديد
    Route::post('/invitations/{invitation}/guests', [InvitationGuestController::class, 'store']);

    // حذف مدعو
    Route::delete('/guests/{guest}', [InvitationGuestController::class, 'destroy']);








    
   // شاشة إضافة دعوة جديدة (تحميل البيانات اللازمة للفورم)
    // Route::get('invitations/add-page', [InvitationController::class, 'addPage']);





        // قائمة الدعوات الخاصة بالمستخدم
    // Route::get('/invitations', [InvitationController::class, 'index']);

    // إنشاء دعوة جديدة
    // Route::post('/invitations', [InvitationController::class, 'store']);

    // عرض دعوة واحدة (للمستخدم المالك فقط)
    // Route::get('/invitations/{id}', [InvitationController::class, 'show']);

    // تحديث دعوة
    // Route::put('/invitations/{id}', [InvitationController::class, 'update']);
    // Route::patch('/invitations/{id}', [InvitationController::class, 'update']);

    // حذف دعوة
    // Route::delete('/invitations/{id}', [InvitationController::class, 'destroy']);

    // نشر الدعوة (تصير Public)
    // Route::post('/invitations/{id}/publish', [InvitationController::class, 'publish']);

    // إلغاء نشر الدعوة (ترجع درافت)
    // Route::post('/invitations/{id}/unpublish', [InvitationController::class, 'unpublish']);

    // store gest invitation
    // Route::post('/invitations/{invitationId}/guests', [InvitationGuestController::class, 'store']);


    
    
    // قائمة المدعوين لدعوة معيّنة
    Route::get('/invitations/{invitation}/guests', [InvitationGuestController::class, 'index']);

    // إضافة مدعو جديد
    Route::post('/invitations/{invitation}/guests', [InvitationGuestController::class, 'store']);

    // حذف مدعو
    // Route::delete('/guests/{guest}', [InvitationGuestController::class, 'destroy']);


    

    // API Public للمدعو
Route::get('/guest/invitations/{token}', [GuestInvitationController::class, 'show']);
Route::post('/guest/invitations/{token}/rsvp', [GuestInvitationController::class, 'submitRsvp']);





        // إنشاء عملية شراء باقة
    Route::post('/plan-purchases', [UserPlanPurchaseController::class, 'store']);

    // قائمة مشتريات المستخدم
    Route::get('/plan-purchases', [UserPlanPurchaseController::class, 'index']);

    // عرض تفاصيل شراء واحد
    Route::get('/plan-purchases/{id}', [UserPlanPurchaseController::class, 'show']);

    // رصيد الدعوات الحالي (مفيد للتطبيق)
    Route::get('/me/quota', [UserPlanPurchaseController::class, 'quota']);







    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/my-events', [MyEventsController::class, 'index']);

    Route::post('/qr/scan', [QrController::class, 'scan']);


    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::patch('/profile', [ProfileController::class, 'update']);

        Route::get('/plans/page', [PlanController::class, 'page']);

    // قائمة الباقات (لو حاب تستخدمها مستقلة)
    Route::get('/plans', [PlanController::class, 'index']);
    Route::get('/plans/{id}', [PlanController::class, 'show']);

    

     Route::get('/invitations/{id}/detail', [InvitationController::class, 'detail']);

    //     Route::post('/plan-purchases', [UserPlanPurchaseController::class, 'store']);
    // Route::get('/plan-purchases', [UserPlanPurchaseController::class, 'index']);
    // Route::get('/plan-purchases/{id}', [UserPlanPurchaseController::class, 'show']);
    // Route::get('/me/quota', [UserPlanPurchaseController::class, 'quota']);


    /*
    |--------------------------------------------------------------------------
    | Vendor app (restaurant side)
    | user.type = 'vendor' (we'll check this later in controllers/middleware)
    |--------------------------------------------------------------------------
    */

    Route::prefix('vendor')->name('vendor.')->group(function () {

        // Vendor profile (joins users + vendors table)
        Route::get('profile',  [VendorProfileController::class, 'show']);
        Route::put('profile',  [VendorProfileController::class, 'update']);

        // Vendor orders (the “add order” screen in the app)
        Route::get('orders',        [VendorOrderController::class, 'index']);   // list vendor orders
        Route::post('orders',       [VendorOrderController::class, 'store']);   // create new delivery request
        Route::get('orders/{order}', [VendorOrderController::class, 'show']);
        Route::put('orders/{order}', [VendorOrderController::class, 'update']);
        Route::delete('orders/{order}', [VendorOrderController::class, 'destroy']);
          Route::post('orders/{order}/cancel', [VendorOrderController::class, 'cancel'])
            ->name('orders.cancel');
    });

    /*
    |--------------------------------------------------------------------------
    | Driver app
    | user.type = 'driver'  (we’ll also enforce this later)
    |--------------------------------------------------------------------------
    */

    Route::prefix('driver')->name('driver.')->group(function () {

        // Driver profile (users + driver_profiles table)
        Route::get('profile', [DriverProfileController::class, 'show']);
        Route::post('profile', [DriverProfileController::class, 'update']);

        // Orders for drivers
        Route::get('orders/available', [DriverOrderController::class, 'available']); // list open jobs
        Route::get('orders/current',   [DriverOrderController::class, 'current']);   // accepted but not finished
        Route::get('orders/history',   [DriverOrderController::class, 'history']);   // finished/cancelled

        Route::post('orders/{order}/pickup',  [DriverOrderController::class, 'pickup']);
        Route::post('orders/{order}/complete',[DriverOrderController::class, 'complete']);

        // Actions on a specific order
        Route::post('orders/{order}/accept',   [DriverOrderController::class, 'accept']);
        Route::post('orders/{order}/start',    [DriverOrderController::class, 'markAsDelivering']);
        // Route::post('orders/{order}/complete', [DriverOrderController::class, 'markAsDelivered']);
        Route::post('orders/{order}/cancel',   [DriverOrderController::class, 'cancel']);
    });

    
    Route::post('/set_fcm_token', [AuthController::class, 'setFcmToken']);










    Route::get('get_access_token', function(){
        $keyFilePath = storage_path('app/firebase/quickmart-af5b6-firebase-adminsdk-fbsvc-3367f8756e.json');

        $googleAuthUrl = "https://oauth2.googleapis.com/token";
    
        // Load service account JSON
        $serviceAccount = json_decode(file_get_contents($keyFilePath), true);
    
        $jwtHeader = base64_encode(json_encode(["alg" => "RS256", "typ" => "JWT"]));
        
        $iat = time();
        $exp = $iat + 3600;
        
        $jwtPayload = base64_encode(json_encode([
            "iss" => $serviceAccount["client_email"],
            "scope" => "https://www.googleapis.com/auth/firebase.messaging",
            "aud" => $googleAuthUrl,
            "exp" => $exp,
            "iat" => $iat
        ]));

        if (!file_exists($keyFilePath)) {
            return response()->json(['error' => 'Service account JSON file not found'], 404);
        }
    
        $signature = "";
        openssl_sign("$jwtHeader.$jwtPayload", $signature, openssl_pkey_get_private($serviceAccount["private_key"]), OPENSSL_ALGO_SHA256);
        $jwtAssertion = "$jwtHeader.$jwtPayload." . base64_encode($signature);
    
        // Send request to get access token
        $response = Http::post($googleAuthUrl, [
            "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
            "assertion" => $jwtAssertion
        ]);
    
        return $response->json()["access_token"] ?? null;
    });
    

    Route::post('send_notification', [ApiHomeController::class, 'sendNotification']);
    Route::post('send_notification_to_topic', [ApiHomeController::class, 'sendNotificationToTopic']);

});
