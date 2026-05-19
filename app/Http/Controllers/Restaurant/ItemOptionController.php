<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\ItemOptionGroup;
use App\Models\ItemOption;
use App\Support\CurrentBranch;
use Illuminate\Http\Request;

class ItemOptionController extends Controller
{
    use CurrentBranch;

    /**
     * عرض كل الخيارات (Options) داخل Group معين لصنف معيّن.
     */
    public function index(MenuItem $item, ItemOptionGroup $group)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        // تأكيد إن الصنف يتبع نفس المطعم والفرع
        $this->authorizeItem($restaurant->id, $branch->id, $item);
        // تأكيد إن الجروب يتبع نفس الصنف
        $this->authorizeGroup($item, $group);

        $options = ItemOption::query()
            ->where('group_id', $group->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(30);

        return view('restaurant.item_options.index', [
            'restaurant' => $restaurant,
            'branch'     => $branch,
            'item'       => $item,
            'group'      => $group,
            'options'    => $options,
        ]);
    }

    /**
     * فورم إنشاء Option جديد داخل Group.
     */
    public function create(MenuItem $item, ItemOptionGroup $group)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeItem($restaurant->id, $branch->id, $item);
        $this->authorizeGroup($item, $group);

        return view('restaurant.item_options.create', [
            'restaurant' => $restaurant,
            'branch'     => $branch,
            'item'       => $item,
            'group'      => $group,
            'option'     => null,
        ]);
    }

    /**
     * حفظ Option جديد.
     */
    public function store(Request $request, MenuItem $item, ItemOptionGroup $group)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeItem($restaurant->id, $branch->id, $item);
        $this->authorizeGroup($item, $group);

        $data = $this->validateData($request);

        $data['group_id']  = $group->id;
        $data['is_active'] = $request->boolean('is_active', true);

        // sort_order افتراضي لو مش متحدد
        if (! isset($data['sort_order']) || $data['sort_order'] === null) {
            $maxSort = ItemOption::where('group_id', $group->id)->max('sort_order');
            $data['sort_order'] = $maxSort ? $maxSort + 1 : 1;
        }

        ItemOption::create($data);

        return redirect()
            ->route('restaurant.items.option-groups.options.index', [$item, $group])
            ->with('success', 'تم إضافة الخيار بنجاح.');
    }

    /**
     * فورم تعديل Option.
     */
    public function edit(MenuItem $item, ItemOptionGroup $group, ItemOption $option)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeItem($restaurant->id, $branch->id, $item);
        $this->authorizeGroup($item, $group);
        $this->authorizeOption($group, $option);

        return view('restaurant.item_options.edit', [
            'restaurant' => $restaurant,
            'branch'     => $branch,
            'item'       => $item,
            'group'      => $group,
            'option'     => $option,
        ]);
    }

    /**
     * تحديث بيانات Option.
     */
    public function update(Request $request, MenuItem $item, ItemOptionGroup $group, ItemOption $option)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeItem($restaurant->id, $branch->id, $item);
        $this->authorizeGroup($item, $group);
        $this->authorizeOption($group, $option);

        $data = $this->validateData($request);

        if (! isset($data['sort_order']) || $data['sort_order'] === null) {
            $data['sort_order'] = $option->sort_order;
        }

        $data['is_active'] = $request->boolean('is_active', $option->is_active);

        $option->update($data);

        return redirect()
            ->route('restaurant.items.option-groups.options.index', [$item, $group])
            ->with('success', 'تم تحديث الخيار بنجاح.');
    }

    /**
     * حذف خيار.
     */
    public function destroy(MenuItem $item, ItemOptionGroup $group, ItemOption $option)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeItem($restaurant->id, $branch->id, $item);
        $this->authorizeGroup($item, $group);
        $this->authorizeOption($group, $option);

        $option->delete();

        return redirect()
            ->route('restaurant.items.option-groups.options.index', [$item, $group])
            ->with('success', 'تم حذف الخيار بنجاح.');
    }

    /**
     * تفعيل / تعطيل خيار.
     */
    public function toggleStatus(MenuItem $item, ItemOptionGroup $group, ItemOption $option)
    {
        $restaurant = $this->currentRestaurantOrAbort();
        $branch     = $this->currentBranchOrAbort();

        $this->authorizeItem($restaurant->id, $branch->id, $item);
        $this->authorizeGroup($item, $group);
        $this->authorizeOption($group, $option);

        $option->is_active = ! $option->is_active;
        $option->save();

        return redirect()
            ->route('restaurant.items.option-groups.options.index', [$item, $group])
            ->with('success', 'تم تحديث حالة الخيار بنجاح.');
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
            (int) $item->restaurant_id !== $restaurantId ||
            (int) $item->branch_id     !== $branchId
        ) {
            abort(403, 'غير مسموح لك بالوصول لهذا الصنف.');
        }
    }

    /**
     * تأكيد أن الـ group يتبع نفس الـ item.
     */
    protected function authorizeGroup(MenuItem $item, ItemOptionGroup $group): void
    {
        if ((int) $group->menu_item_id !== (int) $item->id) {
            abort(403, 'هذه المجموعة لا تتبع هذا الصنف.');
        }
    }

    /**
     * تأكيد أن الـ option يتبع نفس الـ group.
     */
    protected function authorizeOption(ItemOptionGroup $group, ItemOption $option): void
    {
        if ((int) $option->group_id !== (int) $group->id) {
            abort(403, 'هذا الخيار لا يتبع هذه المجموعة.');
        }
    }

    /**
     * فاليديشن مشترك للـ store / update.
     */
    protected function validateData(Request $request): array
    {
        return $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'price_delta' => ['nullable', 'numeric', 'min:0'],
            'sort_order'  => ['nullable', 'integer'],
            'is_active'   => ['nullable', 'boolean'],
        ]);
    }
}
