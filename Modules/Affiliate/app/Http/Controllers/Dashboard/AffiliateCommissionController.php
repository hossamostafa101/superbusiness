<?php

namespace Modules\Affiliate\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Affiliate\Models\AffiliateSetting;

class AffiliateCommissionController extends Controller
{
    public function index(Request $request)
    {
        $profile = Auth::user()
            ->affiliateProfile()
            ->firstOrFail();

        $settings = AffiliateSetting::current();

        $commissions = $profile->commissions()
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('affiliate::dashboard.commissions.index', compact(
            'profile',
            'settings',
            'commissions'
        ));
    }
}