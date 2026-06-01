<?php

namespace Modules\Affiliate\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Affiliate\Models\AffiliateCommission;
use Modules\Affiliate\Models\AffiliateProfile;
use Modules\Affiliate\Models\AffiliateReferral;
use Modules\Affiliate\Models\AffiliateWithdrawal;

class AffiliateAdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'profiles' => AffiliateProfile::query()->count(),
            'active_profiles' => AffiliateProfile::query()->where('status', 'active')->count(),
            'referrals' => AffiliateReferral::query()->count(),
            'commissions_total' => AffiliateCommission::query()->sum('amount'),
            'commissions_pending' => AffiliateCommission::query()->where('status', 'pending')->sum('amount'),
            'commissions_available' => AffiliateCommission::query()->where('status', 'available')->sum('amount'),
            'withdrawals_requested' => AffiliateWithdrawal::query()->where('status', 'requested')->count(),
        ];

        $latestProfiles = AffiliateProfile::query()
            ->latest('id')
            ->limit(8)
            ->get();

        $latestWithdrawals = AffiliateWithdrawal::query()
            ->with('affiliateProfile')
            ->latest('id')
            ->limit(8)
            ->get();

        return view('affiliate::admin.index', compact(
            'stats',
            'latestProfiles',
            'latestWithdrawals'
        ));
    }
}