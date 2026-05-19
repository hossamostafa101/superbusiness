<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Pivots\RestaurantUser;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RestaurantAuthController extends Controller
{
    /**
     * عرض فورم تسجيل المطعم.
     */
    public function showRegisterForm()
    {
        return view('auth.restaurant.register');
    }

    /**
     * تنفيذ تسجيل المطعم:
     * - إنشاء User من نوع restaurant
     * - إنشاء Restaurant
     * - ربطهما في pivot كـ OWNER
     * - تسجيل الدخول
     * - تحويل لصفحة اختيار الخطة
     */
    public function register(Request $request)
{
    $data = $request->validate([
        'restaurant_name' => ['required', 'string', 'max:150'],
        'email'           => ['required', 'email', 'max:190', 'unique:users,email'],
        'phone'           => ['nullable', 'string', 'max:30', 'unique:users,phone'],
        'password'        => ['required', 'string', 'min:6', 'confirmed'],
    ]);

    DB::beginTransaction();

    try {
        // 1) إنشاء حساب User من نوع مطعم
        /** @var \App\Models\User $user */
        $user = User::create([
            'name'        => $data['restaurant_name'],   // اسم الحساب = اسم المطعم
            'username'    => null,
            'email'       => $data['email'],
            'phone'       => $data['phone'] ?? null,
            'password'    => $data['password'],          // هيتعمله hash من الـ cast
            'type'        => User::TYPE_RESTAURANT,
            'is_admin'    => 0,
            'status'      => 'active',                   // تأكد إن عندك العمود ده في جدول users
            'approved_at' => now(),                      // وبرضه العمود ده
        ]);

        // 2) إنشاء المطعم نفسه
        $slug = $this->generateUniqueSlug($data['restaurant_name']);

        /** @var \App\Models\Restaurant $restaurant */
        $restaurant = Restaurant::create([
            'name'      => $data['restaurant_name'],
            'slug'      => $slug,
            'phone'     => $data['phone'] ?? null,
            'email'     => $data['email'],
            'logo_path' => null,
            'currency'  => 'EGP',    // لو جدول restaurants مش فيه currency، يا إمّا تضيفه في migration يا إمّا تشيله من هنا
            'is_active' => true,     // تأكد من وجود العمود برضه
        ]);


        // بعد إنشاء $restaurant
$mainBranch = $restaurant->branches()->create([
    'name'      => 'الفرع الرئيسي',
    'slug'      => $this->generateBranchSlug($restaurant->name),
    'city'      => null,
    'address'   => null,
    'phone'     => $restaurant->phone,
    'is_active' => true,
    'is_main'   => true,
]);



        // 3) ربط الحساب بالمطعم كـ Owner في جدول restaurant_user
        $restaurant->users()->attach($user->id, [
            'role'      => RestaurantUser::ROLE_OWNER,
            'branch_id' => $mainBranch->id,
        ]);

        DB::commit();

        // 4) تسجيل الدخول
        Auth::login($user);

        return redirect()
            ->route('restaurant.plans.index')
            ->with('success', 'تم إنشاء حساب المطعم بنجاح، برجاء اختيار الخطة المناسبة.');
    } catch (\Throwable $e) {
        DB::rollBack();

        if (config('app.debug')) {
            throw $e;   // في بيئة التطوير: هتشوف الخطأ الحقيقي
        }

        report($e);

        return back()
            ->withErrors(['register' => 'حدث خطأ أثناء إنشاء الحساب، حاول مرة أخرى.'])
            ->withInput();
    }
}


    /**
     * عرض فورم تسجيل الدخول للمطعم.
     */
    public function showLoginForm()
    {
        return view('auth.restaurant.login');
    }

    /**
     * تنفيذ تسجيل الدخول لحساب مطعم فقط (type = restaurant).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // نحصر تسجيل الدخول على type = restaurant
        $remember = $request->boolean('remember', false);

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'type' => User::TYPE_RESTAURANT,
        ], $remember)) {
            $request->session()->regenerate();

            // لو عندك Dashboard للمطعم ممكن تستخدمه، لكن الآن نوجهه لصفحة الخطط
            return redirect()->intended(route('restaurant.plans.index'));
        }

        return back()
            ->withErrors([
                'email' => 'بيانات الدخول غير صحيحة أو أن هذا الحساب ليس حساب مطعم.',
            ])
            ->onlyInput('email');
    }

    /**
     * تسجيل خروج المطعم.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('restaurant.login');
    }

    /**
     * توليد slug فريد للمطعم.
     */
    protected function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Restaurant::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    protected function generateBranchSlug(string $restaurantName): string
{
    $base = \Illuminate\Support\Str::slug($restaurantName . '-main');
    $slug = $base;
    $i    = 1;

    while (Branch::where('slug', $slug)->exists()) {
        $slug = $base . '-' . $i;
        $i++;
    }

    return $slug;
}

}
