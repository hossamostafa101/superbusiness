<?php

namespace Modules\Affiliate\Services;

use App\Models\Specification;
use Illuminate\Support\Str;
use Modules\Affiliate\Models\AffiliateLink;
use Modules\Affiliate\Models\AffiliateProfile;

class AffiliateLinkService
{
    public function ensureDefaultLinksForProfile(AffiliateProfile $profile): void
    {
        $this->ensureGeneralLink($profile);

        $specifications = Specification::query()
            ->orderBy('name')
            ->get(['id', 'name', 'key']);

        foreach ($specifications as $specification) {
            $this->ensureSpecificationLink($profile, $specification);
        }
    }

    public function ensureGeneralLink(AffiliateProfile $profile): AffiliateLink
    {
        $trackingUrl = route('public.affiliate.track', $profile->code);

        return AffiliateLink::query()->firstOrCreate(
            [
                'affiliate_profile_id' => $profile->id,
                'specification_id' => null,
                'slug' => 'general',
            ],
            [
                'title' => 'الرابط العام',
                'target_url' => url('/'),
                'tracking_url' => $trackingUrl,
                'is_active' => true,
            ]
        );
    }

public function ensureSpecificationLink(AffiliateProfile $profile, Specification $specification): AffiliateLink
{
    $slug = $specification->key ?? null;

    if (! $slug) {
        $slug = \Illuminate\Support\Str::slug($specification->name);
    }

    $targetUrl = $this->targetUrlForSpecification($specification, $slug);

    $trackingUrl = route('public.affiliate.track', $profile->code)
        . '?target=' . urlencode($targetUrl);

    return AffiliateLink::query()->updateOrCreate(
        [
            'affiliate_profile_id' => $profile->id,
            'specification_id' => $specification->id,
            'slug' => $slug,
        ],
        [
            'title' => 'رابط ' . $specification->name,
            'target_url' => $targetUrl,
            'tracking_url' => $trackingUrl,
            'is_active' => true,
        ]
    );
}

private function targetUrlForSpecification(Specification $specification, string $slug): string
{
    /*
     * عدّل هذه الروابط حسب صفحات الهبوط الفعلية عندك.
     */
    return match ($specification->key ?? $slug) {
        'restaurant', 'restaurants', 'menu' => url('/restaurant-menu'),
        'medical', 'clinic', 'clinics' => url('/medical'),

        default => url('/' . $slug),
    };
}
}