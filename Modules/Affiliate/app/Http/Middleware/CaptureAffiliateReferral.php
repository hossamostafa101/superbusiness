<?php

namespace Modules\Affiliate\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Affiliate\Models\AffiliateProfile;
use Modules\Affiliate\Services\AffiliateTrackingService;

class CaptureAffiliateReferral
{
    public function handle(Request $request, Closure $next)
    {
        $code = $request->query('ref');

        if ($code) {
            $profile = AffiliateProfile::query()
                ->where('code', $code)
                ->where('status', 'active')
                ->first();

            if ($profile) {
                app(AffiliateTrackingService::class)->trackClick($request, $profile->code);
            }
        }

        return $next($request);
    }
}