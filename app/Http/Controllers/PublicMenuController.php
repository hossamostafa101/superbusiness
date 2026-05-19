<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Branch;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Services\MenuStyleService;
use Illuminate\Http\Request;

class PublicMenuController extends Controller
{
    protected MenuStyleService $menuStyleService;

    public function __construct(MenuStyleService $menuStyleService)
    {
        $this->menuStyleService = $menuStyleService;
    }

    public function show(Request $request, Restaurant $restaurant, ?Branch $branch = null)
    {
        // لو الفرع مش متبعت في الـ URL، خُد أول فرع نشط
        if (! $branch) {
            $branch = $restaurant->branches()
                ->where('is_active', true)
                ->orderBy('id')
                ->firstOrFail();
        }

        // بيانات المطعم للـ Blade
        $restaurantPayload = [
            'id'          => $restaurant->id,
            'name'        => $restaurant->name,
            'branch'      => $branch->name,
            'min_order'   => $restaurant->min_order ?? 0,              // عدّل حسب جدولك
            'eta'         => $restaurant->eta_text ?? '30–45 دقيقة',   // تقدير وقت التوصيل
            'cover_color' => $restaurant->brand_color ?? '#0d6efd',    // لون بسيط
            'whatsapp'    => $restaurant->phone,                       // أو رقم واتساب مخصص
        ];

        // التصنيفات الخاصة بالفرع
        $categories = MenuCategory::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('branch_id', $branch->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function ($cat) {
                return [
                    'id'   => $cat->id,
                    'name' => $cat->name,
                    'icon' => $cat->icon_class ?: 'bi-grid', // لو عندك عمود icon
                ];
            })
            ->values()
            ->all();

        // الأصناف + الجروبات + الأوبشنز
        $items = MenuItem::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('branch_id', $branch->id)
            ->where('is_active', true)
            ->with([
                'category',
                'optionGroups' => function ($q) {
                    $q->where('is_active', true)
                      ->orderBy('sort_order')
                      ->orderBy('id')
                      ->with(['options' => function ($qq) {
                          $qq->where('is_active', true)
                             ->orderBy('sort_order')
                             ->orderBy('id');
                      }]);
                },
            ])
            ->orderBy('category_id')
            ->orderBy('id')
            ->get()
            ->map(function (MenuItem $item) {
                // لو فيه offer_price صالح استخدمه
                $basePrice = ($item->offer_price && $item->offer_price > 0 && $item->offer_price < $item->price)
                    ? (float) $item->offer_price
                    : (float) $item->price;

                $mods = $item->optionGroups
                    ->map(function ($group) {
                        return [
                            'name'     => $group->name,
                            'required' => (bool) $group->is_required,
                            'multi'    => (bool) $group->is_multi,
                            'options'  => $group->options
                                ->map(function ($opt) {
                                    return [
                                        'name'  => $opt->name,
                                        'delta' => (float) ($opt->price_delta ?? 0),
                                    ];
                                })
                                ->values()
                                ->all(),
                        ];
                    })
                    ->values()
                    ->all();

                return [
                    'id'          => $item->id,
                    'category_id' => $item->category_id,
                    'name'        => $item->name,
                    'desc'        => $item->description,
                    'price'       => $basePrice,
                    'badge'       => $item->offer_price ? 'عرض خاص' : null,
                    'mods'        => $mods,
                ];
            })
            ->values()
            ->all();

        // 🧠 هنا بنجيب تمبلت كل سكشن (header, categories, items, cart_bar)
        $styles = $this->menuStyleService->getStylesFor($restaurant, $branch);

        
    $preview = $request->boolean('preview', false);
    
        return view('public.menu', [
            'restaurant' => $restaurantPayload,
            'categories' => $categories,
            'items'      => $items,
            'styles'     => $styles,   // 👈 مهم
            'branch'     => $branch,   // لو حبيت تستخدمه في الـ Blade
            
        'preview'    => $preview,
        ]);
    }
}
