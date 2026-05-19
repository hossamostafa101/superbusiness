<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ComparePair;
use App\Models\Device;
use App\Models\NewsArticle;
use App\Models\ProgramAllotment;
use App\Models\Review;
use App\Models\WebBookingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index(Request $request)
    // {
    //     return view('frontend.pages.index');
    // }

    public function index()
{
    if (auth('web')->check()) {
        $workspace = auth('web')->user()
            ->ownedWorkspaces()
            ->latest('id')
            ->first();

        if ($workspace) {
            return redirect()->route('app.business-profile.edit', $workspace);
        }

        return redirect()->route('onboarding.create');
    }

    return view('frontend.pages.index');
}

 public function indexXX()
    {
        if (auth('web')->check()) {
            $user = auth('web')->user();

            $workspace = $user->ownedWorkspaces()
                ->latest('id')
                ->first();

            if (! $workspace) {
                $workspace = $user->workspaces()
                    ->latest('workspaces.id')
                    ->first();
            }

            if ($workspace) {
                return redirect()->route('app.business-profile.edit', $workspace);
            }

            return redirect()->route('onboarding.create');
        }

        return view('home');
    }

    private function fetchDeals(int $limit = 8)
    {
        return Cache::remember("deals.today.v1.$limit", 600, function () use ($limit) {
            $cols = ['id', 'brand_id', 'name', 'image_url'];
            $hasPrice = Schema::hasColumn('devices', 'amazon_price_cents');

            // add optional columns only if they exist
            if (Schema::hasColumn('devices', 'slug'))                    $cols[] = 'slug';
            if ($hasPrice)                                              $cols[] = 'amazon_price_cents';
            if (Schema::hasColumn('devices', 'amazon_currency'))         $cols[] = 'amazon_currency';
            if (Schema::hasColumn('devices', 'amazon_price_checked_at')) $cols[] = 'amazon_price_checked_at';
            if (Schema::hasColumn('devices', 'amazon_price_prev_cents')) $cols[] = 'amazon_price_prev_cents';

            $q = Device::query()->with('brand:id,name')->select($cols);

            if ($hasPrice) {
                $q->whereNotNull('amazon_price_cents');

                if (Schema::hasColumn('devices', 'amazon_price_checked_at')) {
                    $q->where('amazon_price_checked_at', '>=', now()->subDays(14));
                }

                // biggest drop first (if prev exists), otherwise cheapest
                if (Schema::hasColumn('devices', 'amazon_price_prev_cents')) {
                    $q->orderByRaw("
                    CASE
                        WHEN COALESCE(amazon_price_prev_cents,0) > amazon_price_cents
                        THEN (COALESCE(amazon_price_prev_cents,0) - amazon_price_cents)
                        ELSE 0
                    END DESC
                ");
                }
                $q->orderBy('amazon_price_cents', 'asc');
            } else {
                // graceful fallback: just show newest devices
                if (Schema::hasColumn('devices', 'created_at')) $q->orderByDesc('created_at');
                $q->orderByDesc('id');
            }

            $rows = $q->limit($limit)->get();
            $ph = asset('public/frontend/assets/img/qarenly/placeholder-phone.png');

            return $rows->map(function ($d) use ($hasPrice, $ph) {
                $img = $d->image_url ?: $ph;
                $key = $d->slug ?? $d->id;

                $price = ($hasPrice && $d->amazon_price_cents)
                    ? number_format($d->amazon_price_cents / 100, 2)
                    : null;

                $curr = $hasPrice ? ($d->amazon_currency ?: 'USD') : null;

                $old = (Schema::hasColumn('devices', 'amazon_price_prev_cents') && ($d->amazon_price_prev_cents ?? null))
                    ? number_format($d->amazon_price_prev_cents / 100, 2)
                    : null;

                return [
                    'url'   => url('/devices/' . $key),
                    'img'   => $img,
                    'name'  => trim(($d->brand?->name ? $d->brand->name . ' ' : '') . $d->name),
                    'price' => $price,
                    'curr'  => $curr,
                    'old'   => $old,
                ];
            })->values()->all();
        });
    }

    public function about()
    {
        // لو حابب لاحقاً تمرّر إحصائيات ديناميكية للصفحة:
        // $stats = ['countries' => '50+', 'travelers' => '15K'];
        // return view('frontend.pages.about', compact('stats'));

        return view('frontend.pages.about');
    }
}
