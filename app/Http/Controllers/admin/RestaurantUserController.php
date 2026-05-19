<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Branch;
use App\Models\Pivots\RestaurantUser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RestaurantUserController extends Controller
{
    /**
     * عرض كل المستخدمين المرتبطين بمطعم معيّن.
     */
    public function index(Restaurant $restaurant)
    {
        // نجيب المستخدمين مع بيانات الـ pivot + الفرع
        $users = $restaurant->users()
            ->with(['driverProfile', 'vendorProfile']) // لو حابب، أو احذفهم لو مش محتاج
            ->orderBy('users.id', 'desc')
            ->paginate(15);

        return view('admin.sections.restaurants.users.index', compact('restaurant', 'users'));
    }

    /**
     * صفحة إضافة مستخدم جديد للمطعم (ربط مستخدم موجود).
     */
    public function create(Restaurant $restaurant)
    {
        // نجيب المستخدمين من نوع restaurant فقط، عشان ما نربطش customers أو drivers
        $availableUsers = User::query()
            ->where('type', User::TYPE_RESTAURANT)
            ->orderBy('name')
            ->get();

        // فروع المطعم عشان نربط المستخدم بفرع معيّن (اختياري)
        $branches = $restaurant->branches()
            ->orderBy('name')
            ->get();

        return view('admin.sections.restaurants.users.create', compact(
            'restaurant',
            'availableUsers',
            'branches'
        ));
    }

    /**
     * حفظ الربط بين مستخدم ومطعم.
     */
    public function store(Request $request, Restaurant $restaurant)
    {
        $data = $this->validateStore($request, $restaurant);

        // تأكّد إن اليوزر من نوع restaurant
        $user = User::where('id', $data['user_id'])
            ->where('type', User::TYPE_RESTAURANT)
            ->firstOrFail();

        // تأكّد إن الفرع تابع للمطعم لو اتبعت
        $branchId = $data['branch_id'] ?? null;
        if ($branchId) {
            $branchOk = Branch::where('id', $branchId)
                ->where('restaurant_id', $restaurant->id)
                ->exists();

            if (! $branchOk) {
                return back()
                    ->withErrors(['branch_id' => 'الفرع المختار لا يتبع هذا المطعم.'])
                    ->withInput();
            }
        }

        $restaurant->users()->attach($user->id, [
            'role'      => $data['role'],
            'branch_id' => $branchId,
        ]);

        return redirect()
            ->route('admin.restaurants.users.index', $restaurant)
            ->with('success', 'تم ربط المستخدم بالمطعم بنجاح.');
    }

    /**
     * صفحة تعديل بيانات الربط (role + branch) لمستخدم معيّن في مطعم معيّن.
     */
    public function edit(Restaurant $restaurant, User $user)
    {
        // نتأكد إن المستخدم فعلاً مرتبط بالمطعم
        $restaurantUser = $restaurant->users()
            ->where('users.id', $user->id)
            ->firstOrFail();

        $branches = $restaurant->branches()
            ->orderBy('name')
            ->get();

        return view('admin.sections.restaurants.users.edit', compact(
            'restaurant',
            'user',
            'restaurantUser',
            'branches'
        ));
    }

    /**
     * تحديث الـ pivot (role + branch_id) لمستخدم في مطعم.
     */
    public function update(Request $request, Restaurant $restaurant, User $user)
    {
        // نضمن إن المستخدم مرتبط بالمطعم
        $restaurant->users()
            ->where('users.id', $user->id)
            ->firstOrFail();

        $data = $this->validateUpdate($request, $restaurant, $user);

        $branchId = $data['branch_id'] ?? null;
        if ($branchId) {
            $branchOk = Branch::where('id', $branchId)
                ->where('restaurant_id', $restaurant->id)
                ->exists();

            if (! $branchOk) {
                return back()
                    ->withErrors(['branch_id' => 'الفرع المختار لا يتبع هذا المطعم.'])
                    ->withInput();
            }
        }

        $restaurant->users()->updateExistingPivot($user->id, [
            'role'      => $data['role'],
            'branch_id' => $branchId,
        ]);

        return redirect()
            ->route('admin.restaurants.users.index', $restaurant)
            ->with('success', 'تم تحديث بيانات المستخدم في هذا المطعم.');
    }

    /**
     * حذف الربط بين المستخدم والمطعم (مش حذف المستخدم نفسه).
     */
    public function destroy(Restaurant $restaurant, User $user)
    {
        // نتأكد إن كان مرتبط
        $restaurant->users()
            ->where('users.id', $user->id)
            ->firstOrFail();

        $restaurant->users()->detach($user->id);

        return redirect()
            ->route('admin.restaurants.users.index', $restaurant)
            ->with('success', 'تم إزالة المستخدم من هذا المطعم.');
    }

    /**
     * Validation للـ store (إضافة ربط جديد).
     */
    protected function validateStore(Request $request, Restaurant $restaurant): array
    {
        return $request->validate([
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                // ما يتكررش نفس اليوزر داخل نفس المطعم
                Rule::unique('restaurant_user', 'user_id')->where(function ($q) use ($restaurant) {
                    return $q->where('restaurant_id', $restaurant->id);
                }),
            ],
            'role' => [
                'required',
                Rule::in([
                    RestaurantUser::ROLE_OWNER,
                    RestaurantUser::ROLE_MANAGER,
                    RestaurantUser::ROLE_CASHIER,
                    RestaurantUser::ROLE_KITCHEN,
                ]),
            ],
            'branch_id' => [
                'nullable',
                'integer',
                'exists:branches,id',
            ],
        ]);
    }

    /**
     * Validation للـ update (تحديث pivot لمستخدم موجود).
     */
    protected function validateUpdate(Request $request, Restaurant $restaurant, User $user): array
    {
        return $request->validate([
            // مش هنغيّر user_id هنا، فمش محتاجينه
            'role' => [
                'required',
                Rule::in([
                    RestaurantUser::ROLE_OWNER,
                    RestaurantUser::ROLE_MANAGER,
                    RestaurantUser::ROLE_CASHIER,
                    RestaurantUser::ROLE_KITCHEN,
                ]),
            ],
            'branch_id' => [
                'nullable',
                'integer',
                'exists:branches,id',
            ],
        ]);
    }




























        /**
     * صفحة إنشاء "حساب مستخدم جديد" ثم ربطه بالمطعم.
     */
    public function createWithAccount(Restaurant $restaurant)
    {
        // فروع المطعم لاختيار فرع للمستخدم (اختياري)
        $branches = $restaurant->branches()
            ->orderBy('name')
            ->get();

        return view('admin.sections.restaurants.users.create-account', [
            'restaurant'     => $restaurant,
            'branches'       => $branches,
        ]);
    }

    /**
     * حفظ User جديد (type=restaurant) ثم ربطه بالمطعم في جدول restaurant_user.
     */
    public function storeWithAccount(Request $request, Restaurant $restaurant)
    {
        $data = $this->validateNewRestaurantUser($request, $restaurant);

        // تأكد أن الفرع (لو موجود) تابع للمطعم
        $branchId = $data['branch_id'] ?? null;
        if ($branchId) {
            $branchOk = Branch::where('id', $branchId)
                ->where('restaurant_id', $restaurant->id)
                ->exists();

            if (! $branchOk) {
                return back()
                    ->withErrors(['branch_id' => 'الفرع المختار لا يتبع هذا المطعم.'])
                    ->withInput();
            }
        }

        // إنشاء الـ User نفسه
        $user = User::create([
            'name'      => $data['name'],
            'username'  => $data['username'] ?? null,
            'email'     => $data['email'] ?? null,
            'phone'     => $data['phone'] ?? null,
            'password'  => $data['password'], // هيتعمله hash تلقائيًا من الـ cast
            'type'      => User::TYPE_RESTAURANT,
            'is_admin'  => 0,
            'status'    => $data['status'] ?? 'active', // عدّل القيمة حسب النظام عندك
            'approved_at' => now(),
        ]);

        // ربطه بالمطعم في الـ pivot
        $restaurant->users()->attach($user->id, [
            'role'      => $data['role'],
            'branch_id' => $branchId,
        ]);

        return redirect()
            ->route('admin.restaurants.users.index', $restaurant)
            ->with('success', 'تم إنشاء الحساب وربطه بالمطعم بنجاح.');
    }

        /**
     * Validation لإنشاء User جديد مربوط بمطعم.
     */
    protected function validateNewRestaurantUser(Request $request, Restaurant $restaurant): array
    {
        return $request->validate([
            // بيانات الـ User الجديد
            'name' => ['required', 'string', 'max:150'],

            'username' => [
                'nullable',
                'string',
                'max:60',
                'unique:users,username',
            ],

            'email' => [
                'nullable',
                'email',
                'max:190',
                'unique:users,email',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
                'unique:users,phone',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed', // لازم في الفورم يكون عندك password_confirmation
            ],

            // بيانات الربط (pivot)
            'role' => [
                'required',
                Rule::in([
                    RestaurantUser::ROLE_OWNER,
                    RestaurantUser::ROLE_MANAGER,
                    RestaurantUser::ROLE_CASHIER,
                    RestaurantUser::ROLE_KITCHEN,
                ]),
            ],

            'branch_id' => [
                'nullable',
                'integer',
                'exists:branches,id',
            ],

            // لو عايز تسمح بتعديل status من الفورم:
            'status' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);
    }

}
