<?php

namespace Modules\Affiliate\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Affiliate\Models\AffiliateLink;

class AffiliateLinkController extends Controller
{
    public function index()
    {
        $profile = Auth::user()
            ->affiliateProfile()
            ->firstOrFail();

        $links = $profile->links()
            ->with('specification')
            ->latest('id')
            ->get();

        return view('affiliate::dashboard.links.index', compact(
            'profile',
            'links'
        ));
    }
}