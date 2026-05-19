<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ManualPaymentRequest;
use App\Models\Pivots\RestaurantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    /**
     * عرض قائمة الفروع الخاصة بالمطعم الحالي.
     */
    public function index()
    {
        $restaurant = $this->currentRestaurantOrAbort();

        $branches = $restaurant->branches()
            ->orderBy('id', 'desc')
            ->paginate(12);

        return view('restaurant.sections.branches.index', [
            'restaurant' => $restaurant,
            'branches'   => $branches,
        ]);
    }

    /**
     * عرض فورم إنشاء فرع جديد.
     */
    public function create()
    {
        $restaurant = $this->currentRestaurantOrAbort();

        // هنا هنفحص الحد الأقصى للفروع (لو متطبق)
        $this->ensureCanAddBranch($restaurant);

        return view('restaurant.sections.branches.create', [
            'restaurant' => $restaurant,
            'branch'     => null,
        ]);
    }

    /**
     * حفظ فرع جديد.
     */
    public function store(Request $request)
    {
        $restaurant = $this->currentRestaurantOrAbort();

        $this->ensureCanAddBranch($restaurant);

        $data = $this->validateData($request);

        // لو السلاج فاضي نولّده من الاسم
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
        }

        $data['restaurant_id'] = $restaurant->id;
        $data['is_active']     = isset($data['is_active']) ? (bool)$data['is_active'] : true;

        Branch::create($data);

        return redirect()
            ->route('restaurant.branches.index')
            ->with('success', 'تم إضافة الفرع بنجاح.');
    }

    /**
     * فورم تعديل فرع.
     */
    public function edit(Branch $branch)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $this->authorizeBranch($restaurant, $branch);

        return view('restaurant.sections.branches.edit', [
            'restaurant' => $restaurant,
            'branch'     => $branch,
        ]);
    }

    /**
     * تحديث بيانات الفرع.
     */
    public function update(Request $request, Branch $branch)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $this->authorizeBranch($restaurant, $branch);

        $data = $this->validateData($request, $branch->id);

        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $branch->id);
        }

        $data['is_active'] = isset($data['is_active']) ? (bool)$data['is_active'] : $branch->is_active;

        $branch->update($data);

        return redirect()
            ->route('restaurant.branches.index')
            ->with('success', 'تم تحديث بيانات الفرع بنجاح.');
    }

    /**
     * حذف فرع.
     */
    public function destroy(Branch $branch)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $this->authorizeBranch($restaurant, $branch);

        $branch->delete();

        return redirect()
            ->route('restaurant.branches.index')
            ->with('success', 'تم حذف الفرع بنجاح.');
    }

    /**
     * تفعيل / تعطيل الفرع.
     */
    public function toggleStatus(Branch $branch)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $this->authorizeBranch($restaurant, $branch);

        $branch->is_active = ! $branch->is_active;
        $branch->save();

        return redirect()
            ->route('restaurant.branches.index')
            ->with('success', 'تم تحديث حالة الفرع.');
    }

    /* ================== Helpers ================== */

    /**
     * جلب المطعم الحالي لحساب الـ restaurant.
     */
    protected function currentRestaurantOrAbort()
    {
        $user = Auth::user();

        if (! $user || ! $user->isRestaurantAccount()) {
            abort(403);
        }

        // حالياً نفترض أن كل حساب مطعم مرتبط بمطعم واحد فقط
        return $user->restaurants()->firstOrFail();
    }

    /**
     * التأكد أن الفرع يخص نفس المطعم.
     */
    protected function authorizeBranch($restaurant, Branch $branch): void
    {
        if ($branch->restaurant_id !== $restaurant->id) {
            abort(403, 'غير مسموح لك بالوصول لهذا الفرع.');
        }
    }

    /**
     * التحقق من الحد الأقصى للفروع حسب الخطة (يمكن تعديلها لاحقاً).
     *
     * حالياً: لو الخطة فيها عمود max_branches هنستخدمه،
     * لو مش موجود أو null => لا يوجد حد.
     */
    protected function ensureCanAddBranch($restaurant): void
    {
        $subscription = $restaurant->activeSubscription;   // علاقة restaurant->activeSubscription()
        $plan         = $subscription ? $subscription->plan : null;

        // نتوقع مستقبلاً إضافة عمود max_branches في جدول plans
        $maxBranches = $plan->max_branches ?? null;

        if ($maxBranches !== null) {
            $currentCount = $restaurant->branches()->count();
            if ($currentCount >= $maxBranches) {
                abort(403, 'لقد وصلت للحد الأقصى لعدد الفروع في خطتك الحالية.');
            }
        }
        // لو max_branches = null => عدد الفروع مفتوح في هذه الخطة
    }

    /**
     * validation مشترك للـ store و update
     */
    protected function validateData(Request $request, ?int $branchId = null): array
    {
        $slugRule = ['nullable', 'string', 'max:160'];

        // unique في جدول الفروع، مع تجاهل الفرع الحالي في حالة التعديل
        if ($branchId) {
            $slugRule[] = Rule::unique('branches', 'slug')->ignore($branchId);
        } else {
            $slugRule[] = Rule::unique('branches', 'slug');
        }

        return $request->validate([
            'name'    => ['required', 'string', 'max:150'],
            'slug'    => $slugRule,
            'city'    => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'lat'     => ['nullable', 'numeric'],
            'lng'     => ['nullable', 'numeric'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * توليد slug فريد لفرع جديد / تعديل.
     */
    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'branch';
        $slug = $base;
        $i    = 1;

        while (
            Branch::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    public function switch(Branch $branch)
{
    $restaurant = $this->currentRestaurant();

    if ($branch->restaurant_id !== $restaurant->id) {
        abort(403);
    }

    session(['current_branch_id' => $branch->id]);

    return back()->with('success', 'تم تغيير الفرع الحالي إلى: ' . $branch->name);
}

public function switchCurrent(Request $request)
{
    $restaurant = $this->currentRestaurantOrAbort();
    $user       = Auth::user();

    $branchId = (int) $request->input('branch_id');

    $branch = $restaurant->branches()
        ->where('id', $branchId)
        ->firstOrFail();

    // فقط المالك يقدر يغيّر الفرع الحالي
    $isOwner = $restaurant->users()
        ->wherePivot('role', RestaurantUser::ROLE_OWNER)
        ->where('users.id', $user->id)
        ->exists();

    if (! $isOwner) {
        abort(403, 'ليس لديك صلاحية لتغيير الفرع الحالي.');
    }

    session(['current_branch_id' => $branch->id]);

    return back()->with('success', 'تم تغيير الفرع الحالي إلى: ' . $branch->name);
}

}
