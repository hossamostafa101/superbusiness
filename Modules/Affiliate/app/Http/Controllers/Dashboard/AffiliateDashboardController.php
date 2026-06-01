<?php

namespace Modules\Affiliate\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Affiliate\Models\AffiliateSetting;

class AffiliateDashboardController extends Controller
{
    public function index()
    {
        $profile = Auth::user()
            ->affiliateProfile()
            ->withCount([
                'links',
                'referrals',
                'commissions',
                'withdrawals',
            ])
            ->firstOrFail();

        $settings = AffiliateSetting::current();

        $latestCommissions = $profile->commissions()
            ->latest('id')
            ->limit(8)
            ->get();

        return view('affiliate::dashboard.index', compact(
            'profile',
            'settings',
            'latestCommissions'
        ));
    }
}