<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\ItemOptionGroup;
use App\Support\CurrentBranch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemOptionGroupController extends Controller
{
    use CurrentBranch;

    /**
     * عرض كل مجموعات الخيارات لصنف معيّن في الفرع الحالي.
     */
    public function index(MenuItem $item)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeItem($restaurant->id, $branch->id, $item);

        $groups = ItemOptionGroup::query()
            ->where('menu_item_id', $item->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20);

        return view('restaurant.item_option_groups.index', [
            'restaurant' => $restaurant,
            'branch'     => $branch,
            'item'       => $item,
            'groups'     => $groups,
        ]);
    }

    /**
     * فورم إنشاء مجموعة خيارات جديدة (حجم / نوع / إضافات...) لصنف معيّن.
     */
    public function create(MenuItem $item)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeItem($restaurant->id, $branch->id, $item);

        return view('restaurant.item_option_groups.create', [
            'restaurant' => $restaurant,
            'branch'     => $branch,
            'item'       => $item,
            'group'      => null,
        ]);
    }

    /**
     * حفظ مجموعة خيارات جديدة.
     */
    public function store(Request $request, MenuItem $item)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeItem($restaurant->id, $branch->id, $item);

        $data = $this->validateData($request);

        // ربط بالصنف
        $data['menu_item_id'] = $item->id;
        $data['is_required']  = $request->boolean('is_required', false);
        $data['is_multi']     = $request->boolean('is_multi', false);
        $data['is_active']    = $request->boolean('is_active', true);

        // sort_order افتراضي
        if (! isset($data['sort_order']) || $data['sort_order'] === null) {
            $maxSort = ItemOptionGroup::where('menu_item_id', $item->id)->max('sort_order');
            $data['sort_order'] = $maxSort ? $maxSort + 1 : 1;
        }

        ItemOptionGroup::create($data);

        return redirect()
            ->route('restaurant.items.option-groups.index', $item)
            ->with('success', 'تم إضافة مجموعة الخيارات بنجاح.');
    }

    /**
     * فورم تعديل مجموعة خيارات.
     */
    public function edit(MenuItem $item, ItemOptionGroup $group)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeItem($restaurant->id, $branch->id, $item);
        $this->authorizeGroup($item, $group);

        return view('restaurant.item_option_groups.edit', [
            'restaurant' => $restaurant,
            'branch'     => $branch,
            'item'       => $item,
            'group'      => $group,
        ]);
    }

    /**
     * تحديث بيانات مجموعة الخيارات.
     */
    public function update(Request $request, MenuItem $item, ItemOptionGroup $group)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeItem($restaurant->id, $branch->id, $item);
        $this->authorizeGroup($item, $group);

        $data = $this->validateData($request);

        if (! isset($data['sort_order']) || $data['sort_order'] === null) {
            $data['sort_order'] = $group->sort_order;
        }

        $data['is_required'] = $request->boolean('is_required', $group->is_required);
        $data['is_multi']    = $request->boolean('is_multi', $group->is_multi);
        $data['is_active']   = $request->boolean('is_active', $group->is_active);

        $group->update($data);

        return redirect()
            ->route('restaurant.items.option-groups.index', $item)
            ->with('success', 'تم تحديث مجموعة الخيارات بنجاح.');
    }

    /**
     * حذف مجموعة خيارات.
     * (هيتم حذف الـ options التابعة تلقائيًا لو عامل ON DELETE CASCADE).
     */
    public function destroy(MenuItem $item, ItemOptionGroup $group)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeItem($restaurant->id, $branch->id, $item);
        $this->authorizeGroup($item, $group);

        $group->delete();

        return redirect()
            ->route('restaurant.items.option-groups.index', $item)
            ->with('success', 'تم حذف مجموعة الخيارات بنجاح.');
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
     * تأكيد أن الـ item يتبع نفس المطعم والفرع الحاليين.
     */
    protected function authorizeItem(int $restaurantId, int $branchId, MenuItem $item): void
    {
        if (
            (int)$item->restaurant_id !== $restaurantId ||
            (int)$item->branch_id     !== $branchId
        ) {
            abort(403, 'غير مسموح لك بالوصول لهذا الصنف.');
        }
    }

    /**
     * تأكيد أن الـ group يتبع نفس الـ item.
     */
    protected function authorizeGroup(MenuItem $item, ItemOptionGroup $group): void
    {
        if ((int)$group->menu_item_id !== (int)$item->id) {
            abort(403, 'هذه المجموعة لا تتبع هذا الصنف.');
        }
    }

    /**
     * فاليديشن مشترك للـ store/update.
     */
    protected function validateData(Request $request): array
    {
        return $request->validate([
            'name'       => ['required', 'string', 'max:150'],  // مثال: الحجم، النوع، الإضافات
            'type'       => ['nullable', 'string', 'max:50'],   // مثال: size / type / addon
            'sort_order' => ['nullable', 'integer'],
            'is_required'=> ['nullable', 'boolean'],
            'is_multi'   => ['nullable', 'boolean'],
            'is_active'  => ['nullable', 'boolean'],
        ]);
    }
}
