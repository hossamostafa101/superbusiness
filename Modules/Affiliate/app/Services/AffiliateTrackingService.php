<?php

namespace Modules\Affiliate\Services;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Modules\Affiliate\Models\AffiliateLink;
use Modules\Affiliate\Models\AffiliateProfile;
use Modules\Affiliate\Models\AffiliateReferral;

class AffiliateTrackingService
{
    public const COOKIE_NAME = 'affiliate_ref';
    public const SESSION_NAME = 'affiliate_referral_id';

    public function trackClick(Request $request, string $code): ?AffiliateReferral
    {
        $profile = AffiliateProfile::query()
            ->where('code', $code)
            ->where('status', 'active')
            ->first();

        if (! $profile) {
            return null;
        }

        $link = null;

        if ($request->filled('link_id')) {
            $link = AffiliateLink::query()
                ->where('affiliate_profile_id', $profile->id)
                ->where('is_active', true)
                ->find($request->input('link_id'));
        }


        $existingReferralId = session(self::SESSION_NAME)
            ?: $request->cookie(self::SESSION_NAME);

        if ($existingReferralId) {
            $existingReferral = AffiliateReferral::query()
                ->where('id', $existingReferralId)
                ->where('affiliate_profile_id', $profile->id)
                ->first();

            if ($existingReferral) {
                session([
                    self::COOKIE_NAME => $profile->code,
                    self::SESSION_NAME => $existingReferral->id,
                ]);

                return $existingReferral;
            }
        }


        $referral = AffiliateReferral::query()->create([
            'affiliate_profile_id' => $profile->id,
            'affiliate_link_id' => $link?->id,

            'ref_code' => $profile->code,
            'landing_url' => $request->fullUrl(),
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),

            'status' => 'clicked',
            'clicked_at' => now(),
        ]);

        if ($link) {
            $link->increment('clicks_count');
        }

        session([
            self::COOKIE_NAME => $profile->code,
            self::SESSION_NAME => $referral->id,
        ]);

        Cookie::queue(cookie(
            self::COOKIE_NAME,
            $profile->code,
            60 * 24 * 30
        ));

        Cookie::queue(cookie(
            self::SESSION_NAME,
            (string) $referral->id,
            60 * 24 * 30
        ));

        return $referral;
    }

    public function attachRegisteredUser(User $user, ?Workspace $workspace = null): ?AffiliateReferral
    {
        $referralId = session(self::SESSION_NAME)
            ?: request()->cookie(self::SESSION_NAME);

        $refCode = session(self::COOKIE_NAME)
            ?: request()->cookie(self::COOKIE_NAME);

        $referral = null;

        if ($referralId) {
            $referral = AffiliateReferral::query()
                ->where('id', $referralId)
                ->whereNull('referred_user_id')
                ->first();
        }

        if (! $referral && $refCode) {
            $profile = AffiliateProfile::query()
                ->where('code', $refCode)
                ->where('status', 'active')
                ->first();

            if (! $profile) {
                return null;
            }

            $referral = AffiliateReferral::query()->create([
                'affiliate_profile_id' => $profile->id,
                'ref_code' => $profile->code,
                'landing_url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => (string) request()->userAgent(),
                'status' => 'clicked',
                'clicked_at' => now(),
            ]);
        }

        if (! $referral) {
            return null;
        }

        $referral->update([
            'referred_user_id' => $user->id,
            'workspace_id' => $workspace?->id,
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        if ($referral->affiliateLink) {
            $referral->affiliateLink->increment('registrations_count');
        }

        return $referral;
    }

    public function attachWorkspace(User $user, Workspace $workspace): ?AffiliateReferral
    {
        $referral = AffiliateReferral::query()
            ->where('referred_user_id', $user->id)
            ->whereNull('workspace_id')
            ->latest('id')
            ->first();

        if (! $referral) {
            return null;
        }

        $referral->update([
            'workspace_id' => $workspace->id,
        ]);

        return $referral;
    }
}
