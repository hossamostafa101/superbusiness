<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Support\CurrentBranch; // نفس الترايت اللي بتستخدمه في باقي لوحة المطعم (لو عندك)
use Illuminate\Http\Request;

class BranchQrController extends Controller
{
    use CurrentBranch;

    public function show(Branch $branch)
    {
        $restaurant = $this->currentRestaurantOrAbort();

        // تأكيد إن الفرع يتبع المطعم ده
        if ((int) $branch->restaurant_id !== (int) $restaurant->id) {
            abort(403, 'هذا الفرع لا يتبع مطعمك.');
        }

        // رابط المنيو العامة اللي الـ QR هيشاور عليه
        $menuUrl = route('public.menu', [
            'restaurant' => $restaurant->slug,
            'branch'     => $branch->id,
        ]);

        // ألوان افتراضية (تقدر تضيف أعمدة في الـ DB لاحقًا: qr_color, qr_bg_color)
        $qrColor   = $restaurant->qr_color   ?? '#000000'; // أسود
        $qrBgColor = $restaurant->qr_bg_color ?? '#ffffff'; // أبيض

        return view('restaurant.sections.branches.qr', compact(
            'restaurant',
            'branch',
            'menuUrl',
            'qrColor',
            'qrBgColor',
        ));
    }

    protected function currentRestaurantOrAbort()
    {
        $restaurant = $this->currentRestaurant();
        if (! $restaurant) {
            abort(403, 'لا يمكن تحديد المطعم الحالي.');
        }
        return $restaurant;
    }
}
