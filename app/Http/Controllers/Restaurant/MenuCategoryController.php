<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Support\CurrentBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MenuCategoryController extends Controller
{
    use CurrentBranch;

    /**
     * عرض كل التصنيفات للفرع الحالي.
     */
    public function index()
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $categories = MenuCategory::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('branch_id', $branch->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20);

        return view('restaurant.sections.categories.index', [
            'restaurant' => $restaurant,
            'branch'     => $branch,
            'categories' => $categories,
        ]);
    }

    /**
     * فورم إنشاء تصنيف جديد للفرع الحالي.
     */
    public function create()
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        return view('restaurant.sections.categories.create', [
            'restaurant' => $restaurant,
            'branch'     => $branch,
            'category'   => null,
        ]);
    }

    /**
     * حفظ تصنيف جديد.
     */
    public function store(Request $request)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $data = $this->validateData($request, $restaurant->id, $branch->id);

        // لو الـ slug فاضي نولّده من الاسم
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $restaurant->id, $branch->id);
        }

        // ربط بالـ restaurant والفرع الحالي
        $data['restaurant_id'] = $restaurant->id;
        $data['branch_id']     = $branch->id;

        // لو sort_order مش متبعت، نخليها آخر ترتيب + 1
        if (! isset($data['sort_order']) || $data['sort_order'] === null) {
            $maxSort = MenuCategory::where('restaurant_id', $restaurant->id)
                ->where('branch_id', $branch->id)
                ->max('sort_order');

            $data['sort_order'] = $maxSort ? $maxSort + 1 : 1;
        }

        $data['is_active'] = $request->boolean('is_active', true);

        MenuCategory::create($data);

        return redirect()
            ->route('restaurant.categories.index')
            ->with('success', 'تم إضافة التصنيف بنجاح.');
    }

    /**
     * فورم تعديل تصنيف.
     */
    public function edit(MenuCategory $category)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeCategory($restaurant, $branch, $category);

        return view('restaurant.sections.categories.edit', [
            'restaurant' => $restaurant,
            'branch'     => $branch,
            'category'   => $category,
        ]);
    }

    /**
     * تحديث بيانات التصنيف.
     */
    public function update(Request $request, MenuCategory $category)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeCategory($restaurant, $branch, $category);

        $data = $this->validateData($request, $restaurant->id, $branch->id, $category);

        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $restaurant->id, $branch->id, $category->id);
        }

        if (! isset($data['sort_order']) || $data['sort_order'] === null) {
            // لو ما حددش sort_order خليه يفضل زي ما هو
            $data['sort_order'] = $category->sort_order;
        }

        $data['is_active'] = $request->boolean('is_active', $category->is_active);

        $category->update($data);

        return redirect()
            ->route('restaurant.categories.index')
            ->with('success', 'تم تحديث التصنيف بنجاح.');
    }

    /**
     * حذف تصنيف.
     */
    public function destroy(MenuCategory $category)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeCategory($restaurant, $branch, $category);

        // TODO: لو عندك Items مربوطة بهذا التصنيف لازم تعالجها (منع الحذف / حذف تابع)
        $category->delete();

        return redirect()
            ->route('restaurant.categories.index')
            ->with('success', 'تم حذف التصنيف بنجاح.');
    }

    /* ================= Helpers ================= */

    protected function currentRestaurantOrAbort()
    {
        $restaurant = $this->currentRestaurant();
        if (! $restaurant) {
            abort(403, 'لا يمكن تحديد المطعم الحالي.');
        }
        return $restaurant;
    }

    protected function currentBranchOrAbort()
    {
        $branch = $this->currentBranch();
        if (! $branch) {
            abort(403, 'لا يمكن تحديد الفرع الحالي.');
        }
        return $branch;
    }

    /**
     * التأكد أن التصنيف يتبع نفس المطعم ونفس الفرع.
     */
    protected function authorizeCategory($restaurant, $branch, MenuCategory $category): void
    {
        if (
            $category->restaurant_id !== $restaurant->id ||
            $category->branch_id     !== $branch->id
        ) {
            abort(403, 'غير مسموح لك بالوصول لهذا التصنيف.');
        }
    }

    /**
     * فاليديشن مشترك للـ store و update
     */
    protected function validateData(Request $request, int $restaurantId, int $branchId, ?MenuCategory $category = null): array
    {
        $uniqueSlug = Rule::unique('menu_categories', 'slug')
            ->where(function ($q) use ($restaurantId, $branchId) {
                $q->where('restaurant_id', $restaurantId)
                  ->where('branch_id', $branchId);
            });

        if ($category) {
            $uniqueSlug->ignore($category->id);
        }

        return $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'slug'       => ['nullable', 'string', 'max:160', $uniqueSlug],
            'sort_order' => ['nullable', 'integer'],
            'is_active'  => ['nullable', 'boolean'],
        ]);
    }

    /**
     * توليد slug فريد داخل نفس المطعم والفرع.
     */
    protected function generateUniqueSlug(string $name, int $restaurantId, int $branchId, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'category';
        $slug = $base;
        $i    = 1;

        while (
            MenuCategory::where('restaurant_id', $restaurantId)
                ->where('branch_id', $branchId)
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
