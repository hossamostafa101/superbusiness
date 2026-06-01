<?php

namespace Modules\Affiliate\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Affiliate\Models\AffiliateCommission;
use Modules\Affiliate\Models\AffiliateProfile;
use Modules\Affiliate\Services\AffiliateLinkService;

class AffiliateAdminProfileController extends Controller
{
    public function index(Request $request)
    {
        $profiles = AffiliateProfile::query()
            ->with('user')
            ->withCount([
                'links',
                'referrals',
                'commissions',
                'withdrawals',
            ])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));

                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return view('affiliate::admin.profiles.index', compact('profiles'));
    }

    public function show(AffiliateProfile $profile)
    {
        $profile->load([
            'user',
            'links.specification',
            'referrals.workspace',
            'commissions',
            'withdrawals',
        ]);

        $latestCommissions = $profile->commissions()
            ->latest('id')
            ->limit(15)
            ->get();

        $latestReferrals = $profile->referrals()
            ->with(['workspace', 'referredUser'])
            ->latest('id')
            ->limit(15)
            ->get();

        return view('affiliate::admin.profiles.show', compact(
            'profile',
            'latestCommissions',
            'latestReferrals'
        ));
    }

    public function updateStatus(Request $request, AffiliateProfile $profile)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'active', 'suspended', 'rejected'])],
        ]);

        $payload = [
            'status' => $data['status'],
        ];

        if ($data['status'] === 'active' && ! $profile->approved_at) {
            $payload['approved_at'] = now();
        }

        $profile->update($payload);

        return back()->with('success', 'تم تحديث حالة المسوق.');
    }

    public function storeManualCommission(Request $request, AffiliateProfile $profile)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'max:10'],
            'type' => ['required', Rule::in(['manual_bonus', 'adjustment'])],
            'status' => ['required', Rule::in(['pending', 'available'])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        AffiliateCommission::query()->create([
            'affiliate_profile_id' => $profile->id,

            'type' => $data['type'],
            'base_amount' => 0,

            'commission_type' => 'fixed',
            'commission_value' => $data['amount'],
            'amount' => $data['amount'],
            'currency' => strtoupper($data['currency']),

            'status' => $data['status'],
            'earned_at' => now(),
            'available_at' => $data['status'] === 'available' ? now() : now()->addDays(45),

            'notes' => $data['notes'] ?? null,
        ]);

        $profile->recalculateBalances();

        return back()->with('success', 'تم إضافة العمولة اليدوية.');
    }


    public function generateLinks(AffiliateProfile $profile, AffiliateLinkService $linkService)
{
    $linkService->ensureDefaultLinksForProfile($profile);

    return back()->with('success', 'تم إنشاء / تحديث روابط المسوق.');
}
}