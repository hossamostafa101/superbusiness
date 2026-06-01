<?php

namespace Modules\Affiliate\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Affiliate\Models\AffiliateSetting;
use Modules\Affiliate\Services\AffiliateWithdrawalService;

class AffiliateWithdrawalController extends Controller
{
    public function index()
    {
        $profile = Auth::user()->affiliateProfile()->firstOrFail();

        $profile->recalculateBalances();
        $profile->refresh();

        $settings = AffiliateSetting::current();

        $withdrawals = $profile->withdrawals()
            ->latest('id')
            ->paginate(20);

        return view('affiliate::dashboard.withdrawals.index', compact(
            'profile',
            'settings',
            'withdrawals'
        ));
    }

    public function store(Request $request, AffiliateWithdrawalService $withdrawalService)
    {
        $profile = Auth::user()->affiliateProfile()->firstOrFail();

        $data = $request->validate([
            'payment_method' => ['required', 'in:bank_transfer,wallet,cash,other'],
            'payment_details' => ['required', 'string', 'max:2000'],
            'affiliate_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $withdrawalService->requestWithdrawal($profile, $data);
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم إرسال طلب السحب بنجاح.');
    }
}