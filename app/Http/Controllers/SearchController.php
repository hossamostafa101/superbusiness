<?php
// app/Http/Controllers/SearchController.php
namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Brand;
use Illuminate\Http\Request;

class SearchController extends Controller
{
     public function suggest(Request $request)
    {
        $q = trim((string)($request->q ?? $request->term ?? ''));
        if ($q === '' || mb_strlen($q) < 2) {
            return response()->json(['devices'=>[], 'brands'=>[]]);
        }

        $devices = Device::query()
            ->with('brand:id,name')
            ->select(['id','name','brand_id','image_url','chipset','os'])
            ->where(function($w) use ($q){
                $w->where('name','like',"%{$q}%")
                  ->orWhere('chipset','like',"%{$q}%")
                  ->orWhereHas('brand', fn($b)=>$b->where('name','like',"%{$q}%"));
            })
            ->orderByRaw("CASE WHEN name LIKE ? THEN 0 ELSE 1 END", ["{$q}%"])
            ->orderBy('name')
            ->limit(8)
            ->get()
            ->map(function($d){
                return [
                    'id'    => $d->id,                                // <-- مهم لصفحة المقارنة
                    'name'  => $d->name,
                    'brand' => $d->brand?->name,
                    'chip'  => $d->chipset,
                    'os'    => $d->os,
                    'url'   => url('/devices/'.$d->id),               // يبقى للهيدر
                    'img'   => $d->image_url ?: asset('public/frontend/assets/img/qarenly/placeholder-phone.png'),
                ];
            })
            ->values();

        $brands = Brand::query()
            ->select(['id','name'])
            ->where('name','like',"%{$q}%")
            ->orderByRaw("CASE WHEN name LIKE ? THEN 0 ELSE 1 END", ["{$q}%"])
            ->orderBy('name')
            ->limit(8)
            ->get()
            ->map(function($b){
                return [
                    'id'   => $b->id,
                    'name' => $b->name,
                    'url'  => url('/devices?brand[]='.rawurlencode($b->name)),
                    'logo' => null,
                ];
            })
            ->values();

        return response()->json(compact('devices','brands'));
    }
    public function suggestX(Request $request)
    {
        $q = trim((string)($request->q ?? $request->term ?? ''));
        if ($q === '' || mb_strlen($q) < 2) {
            // رجّع فاضي لو أقل من حرفين لتقليل الضغط
            return response()->json(['devices'=>[], 'brands'=>[]]);
        }

        // أجهزة: بحث بالاسم + الشيبست + اسم البراند
        $devices = Device::query()
            ->with('brand:id,name')
            ->select(['id','name','brand_id','image_url','chipset','os'])
            ->where(function($w) use ($q){
                $w->where('name','like',"%{$q}%")
                  ->orWhere('chipset','like',"%{$q}%")
                  ->orWhereHas('brand', fn($b)=>$b->where('name','like',"%{$q}%"));
            })
            // يفضّل النتائج التي تبدأ بالمصطلح
            ->orderByRaw("CASE WHEN name LIKE ? THEN 0 ELSE 1 END", ["{$q}%"])
            ->orderBy('name')
            ->limit(6)
            ->get()
            ->map(function($d){
                return [
                    'name'  => $d->name,
                    'brand' => $d->brand?->name,
                    'chip'  => $d->chipset,
                    'os'    => $d->os,
                    'url'   => url('/devices/'.$d->id), // مافيش slug حالياً
                    'img'   => $d->image_url ?: asset('public/frontend/assets/img/qarenly/placeholder-phone.png'),
                ];
            })
            ->values();

        // براندات: بالاسم فقط (مفيش logo_url ولا slug في الجدول حالياً)
        $brands = Brand::query()
            ->select(['id','name'])
            ->where('name','like',"%{$q}%")
            ->orderByRaw("CASE WHEN name LIKE ? THEN 0 ELSE 1 END", ["{$q}%"])
            ->orderBy('name')
            ->limit(8)
            ->get()
            ->map(function($b){
                // نوجّه لصفحة الأجهزة مع فلتر البراند بالاسم (متوافق مع صفحتك)
                return [
                    'name' => $b->name,
                    'url'  => url('/devices?brand[]='.rawurlencode($b->name)),
                    'logo' => null, // حالياً لا يوجد لوجو في الجدول
                ];
            })
            ->values();

        return response()->json(compact('devices','brands'));
    }
}
