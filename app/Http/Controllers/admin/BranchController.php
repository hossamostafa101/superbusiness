<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    /**
     * قائمة الفروع لمطعم معيّن.
     */
    public function index(Restaurant $restaurant)
    {
        $branches = $restaurant->branches()
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.sections.branches.index', compact('restaurant', 'branches'));
    }

    /**
     * صفحة إنشاء فرع جديد للمطعم.
     */
    public function create(Restaurant $restaurant)
    {
        return view('admin.sections.branches.create', compact('restaurant'));
    }

    /**
     * حفظ فرع جديد.
     */
    public function store(Request $request, Restaurant $restaurant)
    {
        $data = $this->validateData($request, $restaurant);

        // slug: لو فاضي يتولّد من الاسم
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active']     = $request->boolean('is_active');
        $data['restaurant_id'] = $restaurant->id;

        Branch::create($data);

        return redirect()
            ->route('admin.restaurants.branches.index', $restaurant)
            ->with('success', 'تم إضافة الفرع بنجاح.');
    }

    /**
     * صفحة تعديل فرع.
     */
    public function edit(Restaurant $restaurant, Branch $branch)
    {
        // لو مش مستخدم scopeBindings تأكد إن الفرع فعلاً تابع للمطعم
        if ($branch->restaurant_id !== $restaurant->id) {
            abort(404);
        }

        return view('admin.sections.branches.edit', compact('restaurant', 'branch'));
    }

    /**
     * تحديث فرع موجود.
     */
    public function update(Request $request, Restaurant $restaurant, Branch $branch)
    {
        if ($branch->restaurant_id !== $restaurant->id) {
            abort(404);
        }

        $data = $this->validateData($request, $restaurant, $branch->id);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active'] = $request->boolean('is_active');

        $branch->update($data);

        return redirect()
            ->route('admin.restaurants.branches.index', $restaurant)
            ->with('success', 'تم تحديث بيانات الفرع بنجاح.');
    }

    /**
     * حذف فرع.
     */
    public function destroy(Restaurant $restaurant, Branch $branch)
    {
        if ($branch->restaurant_id !== $restaurant->id) {
            abort(404);
        }

        $branch->delete();

        return redirect()
            ->route('admin.restaurants.branches.index', $restaurant)
            ->with('success', 'تم حذف الفرع بنجاح.');
    }

    /**
     * تفعيل / تعطيل فرع.
     */
    public function toggleStatus(Restaurant $restaurant, Branch $branch)
    {
        if ($branch->restaurant_id !== $restaurant->id) {
            abort(404);
        }

        $branch->update([
            'is_active' => ! $branch->is_active,
        ]);

        return back()->with('success', 'تم تحديث حالة الفرع بنجاح.');
    }

    /**
     * Validation مشترك بين store / update
     *
     * @param  Restaurant  $restaurant  المطعم
     * @param  int|null    $branchId    في حالة update
     */
    protected function validateData(Request $request, Restaurant $restaurant, ?int $branchId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],

            // slug فريد داخل نفس المطعم
            'slug' => [
                'nullable',
                'string',
                'max:160',
                Rule::unique('branches', 'slug')
                    ->where(fn ($q) => $q->where('restaurant_id', $restaurant->id))
                    ->ignore($branchId),
            ],

            'phone'   => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'city'    => ['nullable', 'string', 'max:120'],

            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],

            // is_active هنتعامل معها كـ boolean في الكود
            'is_active' => ['nullable'],
        ]);
    }
}
