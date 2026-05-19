<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\MenuCategory;
use App\Support\CurrentBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MenuItemController extends Controller
{
    use CurrentBranch;

    /**
     * قائمة الأصناف (items) للفرع الحالي.
     */
    public function index(Request $request)
{
    $restaurant = $this->currentRestaurantOrAbort();
    $branch     = $this->currentBranchOrAbort();

    $query = MenuItem::query()
        ->where('restaurant_id', $restaurant->id)
        ->where('branch_id', $branch->id)
        ->with('category');

    if ($request->filled('category_id')) {
        $query->where('category_id', (int) $request->category_id);
    }

    if ($search = $request->get('q')) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('tags', 'like', "%{$search}%");
        });
    }

    $items = $query
        ->orderBy('id', 'desc')
        ->paginate(20);

    $categories = MenuCategory::query()
        ->where('restaurant_id', $restaurant->id)
        ->where('branch_id', $branch->id)
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();

    return view('restaurant.sections.items.index', compact(
        'restaurant', 'branch', 'items', 'categories'
    ));
}


    /**
     * فورم إضافة صنف جديد.
     */
    public function create()
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        // التصنيفات المتاحة للاختيار (لنفس الفرع)
        $categories = MenuCategory::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('branch_id', $branch->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('restaurant.sections.items.create', [
            'restaurant' => $restaurant,
            'branch'     => $branch,
            'item'       => null,
            'categories' => $categories,
        ]);
    }

    /**
     * حفظ صنف جديد.
     */
   public function store(Request $request)
{
    $restaurant = $this->currentRestaurantOrAbort();
    $branch     = $this->currentBranchOrAbort();

    $data = $this->validateData($request, $restaurant->id, $branch->id);

    // الصورة
    $imageFile = $request->file('image');
    unset($data['image']); // ما نخزنش الـ UploadedFile في الـ DB

    if (empty($data['slug'] ?? null)) {
        $data['slug'] = $this->generateUniqueSlug(
            $data['name'],
            $restaurant->id,
            $branch->id
        );
    }

    $data['restaurant_id'] = $restaurant->id;
    $data['branch_id']     = $branch->id;
    $data['is_active']     = $request->boolean('is_active', true);

    if ($imageFile) {
        $path = $imageFile->store('menu_items', 'public'); // storage/app/public/menu_items
        $data['image_path'] = '/app/public/' . $path;
    }

    MenuItem::create($data);

    return redirect()
        ->route('restaurant.items.index')
        ->with('success', 'تم إضافة الصنف بنجاح.');
}


    /**
     * فورم تعديل صنف.
     */
    public function edit(MenuItem $item)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeItem($restaurant, $branch, $item);

        $categories = MenuCategory::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('branch_id', $branch->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('restaurant.sections.items.edit', [
            'restaurant' => $restaurant,
            'branch'     => $branch,
            'item'       => $item,
            'categories' => $categories,
        ]);
    }

    /**
     * تحديث بيانات الصنف.
     */
    public function update(Request $request, MenuItem $item)
{
    $restaurant = $this->currentRestaurantOrAbort();
    $branch     = $this->currentBranchOrAbort();

    $this->authorizeItem($restaurant, $branch, $item);

    $data = $this->validateData($request, $restaurant->id, $branch->id, $item);

    $imageFile = $request->file('image');
    unset($data['image']);

    if (empty($data['slug'] ?? null)) {
        $data['slug'] = $this->generateUniqueSlug(
            $data['name'],
            $restaurant->id,
            $branch->id,
            $item->id
        );
    }

    $data['is_active'] = $request->boolean('is_active', $item->is_active);

    if ($imageFile) {
        // ممكن هنا تحذف الصورة القديمة لو حابب
        $path = $imageFile->store('menu_items', 'public');
        $data['image_path'] = '/app/public/' . $path;
    }

    $item->update($data);

    return redirect()
        ->route('restaurant.items.index')
        ->with('success', 'تم تحديث بيانات الصنف بنجاح.');
}


    /**
     * حذف صنف.
     */
    public function destroy(MenuItem $item)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeItem($restaurant, $branch, $item);

        // TODO: لو عندك علاقات (order_items) لازم تتعامل معاها (منع / soft delete / إلخ)
        $item->delete();

        return redirect()
            ->route('restaurant.items.index')
            ->with('success', 'تم حذف الصنف بنجاح.');
    }

    /**
     * تفعيل / تعطيل الصنف.
     */
    public function toggleStatus(MenuItem $item)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeItem($restaurant, $branch, $item);

        $item->is_active = ! $item->is_active;
        $item->save();

        return redirect()
            ->route('restaurant.items.index')
            ->with('success', 'تم تحديث حالة الصنف بنجاح.');
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
     * التأكد أن الصنف يتبع نفس المطعم ونفس الفرع الحالي.
     */
    protected function authorizeItem($restaurant, $branch, MenuItem $item): void
    {
        if (
            (int) $item->restaurant_id !== (int) $restaurant->id ||
            (int) $item->branch_id     !== (int) $branch->id
        ) {
            abort(403, 'غير مسموح لك بالوصول لهذا الصنف.');
        }
    }

    /**
     * فاليديشن مشترك للـ store و update.
     */
    protected function validateData(
    Request $request,
    int $restaurantId,
    int $branchId,
    ?MenuItem $item = null
): array {
    $uniqueSlug = Rule::unique('menu_items', 'slug')
        ->where(function ($q) use ($restaurantId, $branchId) {
            $q->where('restaurant_id', $restaurantId)
              ->where('branch_id', $branchId);
        });

    if ($item) {
        $uniqueSlug->ignore($item->id);
    }

    $imageRule = $item ? 'nullable' : 'required';

    $data = $request->validate([
        'name'        => ['required', 'string', 'max:190'],
        'category_id' => [
            'required',
            'integer',
            Rule::exists('menu_categories', 'id')->where(function ($q) use ($restaurantId, $branchId) {
                $q->where('restaurant_id', $restaurantId)
                  ->where('branch_id', $branchId);
            }),
        ],
        'price'       => ['required', 'numeric', 'min:0'],
        'offer_price' => ['nullable', 'numeric', 'min:0'],
        'description' => ['nullable', 'string'],
        'slug'        => ['nullable', 'string', 'max:190', $uniqueSlug],
        'tags'        => ['nullable', 'string', 'max:255'],
        'image'       => [$imageRule, 'image', 'max:5120'], // 5MB
        'is_active'   => ['nullable', 'boolean'],
    ]);

    return $data;
}


    /**
     * توليد slug فريد داخل نفس المطعم والفرع.
     */
    protected function generateUniqueSlug(
        string $name,
        int $restaurantId,
        int $branchId,
        ?int $ignoreId = null
    ): string {
        $base = Str::slug($name) ?: 'item';
        $slug = $base;
        $i    = 1;

        while (
            MenuItem::where('restaurant_id', $restaurantId)
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
