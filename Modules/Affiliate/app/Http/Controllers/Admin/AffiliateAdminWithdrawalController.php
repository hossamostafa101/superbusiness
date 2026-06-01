<?php

namespace Modules\Affiliate\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Affiliate\Models\AffiliateWithdrawal;
use Modules\Affiliate\Services\AffiliateWithdrawalService;

class AffiliateAdminWithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $withdrawals = AffiliateWithdrawal::query()
            ->with('affiliateProfile.user')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('affiliate::admin.withdrawals.index', compact('withdrawals'));
    }

    public function show(AffiliateWithdrawal $withdrawal)
    {
        $withdrawal->load([
            'affiliateProfile.user',
            'commissions.workspace',
            'commissions.referral',
        ]);

        return view('affiliate::admin.withdrawals.show', compact('withdrawal'));
    }

    public function approve(Request $request, AffiliateWithdrawal $withdrawal, AffiliateWithdrawalService $service)
    {
        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $service->approve($withdrawal, $data['admin_notes'] ?? null);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم قبول طلب السحب.');
    }

    public function markPaid(Request $request, AffiliateWithdrawal $withdrawal, AffiliateWithdrawalService $service)
    {
        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $service->markAsPaid($withdrawal, $data['admin_notes'] ?? null);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم تعليم الطلب كمدفوع.');
    }

    public function reject(Request $request, AffiliateWithdrawal $withdrawal, AffiliateWithdrawalService $service)
    {
        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $service->reject($withdrawal, $data['admin_notes'] ?? null);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم رفض طلب السحب.');
    }
}