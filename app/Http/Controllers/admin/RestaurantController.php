<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Pivots\RestaurantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RestaurantController extends Controller
{
    /**
     * عرض قائمة المطاعم مع بحث بسيط.
     */
    public function index(Request $request)
    {
        $restaurants = Restaurant::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('name', 'like', $term)
                       ->orWhere('slug', 'like', $term)
                       ->orWhere('phone', 'like', $term)
                       ->orWhere('email', 'like', $term);
                });
            })
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.sections.restaurants.index', compact('restaurants'));
    }

    /**
     * صفحة إنشاء مطعم جديد.
     */
    public function create()
    {
        // لو حابب تختار owner من Users:
        // هنا بنجيب المستخدمين اللي نوعهم restaurant (أو تقدر تستخدم شرط مختلف)
        $owners = User::query()
            ->where('type', User::TYPE_RESTAURANT)
            ->orderBy('name')
            ->get();

        return view('admin.sections.restaurants.create', compact('owners'));
    }

    /**
     * حفظ مطعم جديد.
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);

        // slug: لو المستخدم ما كتبهاش، نولّدها من الاسم
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // تشيك بوكس is_active
        $data['is_active'] = $request->boolean('is_active');

        // رفع لوجو (اختياري)
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
            $data['logo_path'] = 'app/public/' . $data['logo_path'];
        }

        DB::transaction(function () use ($data, $request) {
            /** @var \App\Models\Restaurant $restaurant */
            $restaurant = Restaurant::create($data);

            // ربط owner لو تم اختياره
            if ($request->filled('owner_id')) {
                $ownerId = (int) $request->input('owner_id');

                // تأكد إن اليوزر موجود
                $owner = User::where('id', $ownerId)
                    ->where('type', User::TYPE_RESTAURANT)
                    ->first();

                if ($owner) {
                    $restaurant->users()->attach($owner->id, [
                        'role'      => RestaurantUser::ROLE_OWNER,
                        'branch_id' => null,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.restaurants.index')
            ->with('success', 'تم إنشاء المطعم بنجاح.');
    }

    /**
     * صفحة تعديل مطعم.
     */
    public function edit(Restaurant $restaurant)
    {
        // نفس قائمة الـ owners
        $owners = User::query()
            ->where('type', User::TYPE_RESTAURANT)
            ->orderBy('name')
            ->get();

        // نجيب owner الحالي (لو موجود) من الـ pivot
        $currentOwnerId = $restaurant->owners()->first()?->id;

        return view('admin.sections.restaurants.edit', compact('restaurant', 'owners', 'currentOwnerId'));
    }

    /**
     * تحديث مطعم موجود.
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        $data = $this->validateData($request, $restaurant->id);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active'] = $request->boolean('is_active');

        // تحديث اللوجو لو فيه ملف مرفوع
        if ($request->hasFile('logo')) {
            // لو حابب تمسح اللوجو القديم من الـ storage تقدر تضيف كود هنا
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
            $data['logo_path'] = 'app/public/' . $data['logo_path'];
        }

        DB::transaction(function () use ($data, $request, $restaurant) {
            $restaurant->update($data);

            // تحديث owner
            if ($request->filled('owner_id')) {
                $ownerId = (int) $request->input('owner_id');

                $owner = User::where('id', $ownerId)
                    ->where('type', User::TYPE_RESTAURANT)
                    ->first();

                if ($owner) {
                    // احذف كل الـ owners الحاليين ثم اربط الجديد
                    $restaurant->owners()->detach();
                    $restaurant->users()->syncWithoutDetaching([
                        $owner->id => [
                            'role'      => RestaurantUser::ROLE_OWNER,
                            'branch_id' => null,
                        ],
                    ]);
                }
            } else {
                // لو ما تم اختيار owner: ممكن تسيبه زي ما هو أو تمسح كل owners
                // هنا هنسيبهم كما هم (ما بنعملش حاجة)
            }
        });

        return redirect()
            ->route('admin.restaurants.index')
            ->with('success', 'تم تحديث بيانات المطعم بنجاح.');
    }

    /**
     * حذف مطعم.
     */
    public function destroy(Restaurant $restaurant)
    {
        // هيتم حذف الفروع والـ pivot حسب الـ foreign keys لو مفعّل عندك ON DELETE CASCADE
        $restaurant->delete();

        return redirect()
            ->route('admin.restaurants.index')
            ->with('success', 'تم حذف المطعم بنجاح.');
    }

    /**
     * تفعيل / تعطيل مطعم.
     */
    public function toggleStatus(Restaurant $restaurant)
    {
        $restaurant->update([
            'is_active' => ! $restaurant->is_active,
        ]);

        return back()->with('success', 'تم تحديث حالة المطعم بنجاح.');
    }

    /**
     * Validation مشترك بين store / update
     *
     * @param  int|null  $ignoreId  لتجاوز unique في حالة update
     */
   protected function validateData(Request $request, ?int $ignoreId = null): array
{
    // لو عايزها كسطر واحد (أسهل)
    $slugRule = 'nullable|string|max:160|unique:restaurants,slug';

    if ($ignoreId) {
        // unique:table,column,ignore_id
        $slugRule .= ',' . $ignoreId;
    }

    return $request->validate([
        'name'      => ['required', 'string', 'max:150'],

        // لاحظ: من غير أقواس []
        'slug'      => $slugRule,

        'phone'     => ['nullable', 'string', 'max:30'],
        'email'     => ['nullable', 'email', 'max:190'],
        'currency'  => ['required', 'string', 'size:3'],

        // الملف بنعالجه كـ image
        'logo'      => ['nullable', 'image', 'max:2048'], // 2MB

        'is_active' => ['nullable'], // هنتعامل معها بـ boolean() في الكود
        'owner_id'  => ['nullable', 'integer', 'exists:users,id'],
    ]);
}
}
