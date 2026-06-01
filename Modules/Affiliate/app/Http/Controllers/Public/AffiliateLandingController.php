<?php

namespace Modules\Affiliate\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Modules\Affiliate\Models\AffiliateProfile;
use Modules\Affiliate\Services\AffiliateRegistrationService;

class AffiliateLandingController extends Controller
{
    public function index()
    {
        return view('affiliate::public.landing');
    }

    public function register()
    {
        return view('affiliate::public.register');
    }

    public function store(Request $request, AffiliateRegistrationService $registrationService)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $existingProfile = AffiliateProfile::query()
            ->whereHas('user', function ($query) use ($data) {
                $query->where('email', $data['email']);
            })
            ->first();

        if ($existingProfile) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'هذا البريد مسجل بالفعل كمسوق.',
                ]);
        }

        $profile = $registrationService->register($data);

        Auth::login($profile->user);

        return redirect()
            ->route('affiliate.dashboard')
            ->with('success', 'تم تسجيلك كمسوق بنجاح.');
    }

    public function track(
    \Illuminate\Http\Request $request,
    string $code,
    \Modules\Affiliate\Services\AffiliateTrackingService $trackingService
) {
    $referral = $trackingService->trackClick($request, $code);

    if (! $referral) {
        return redirect('/');
    }

    /*
     * الأولوية:
     * 1. لو فيه link_id نفتح target_url الخاص بالرابط.
     * 2. لو فيه target نستخدمه.
     * 3. fallback للصفحة الرئيسية.
     */
    $target = null;

    if ($request->filled('link_id') && $referral->affiliateLink) {
        $target = $referral->affiliateLink->target_url;
    }

    if (! $target && $request->filled('target')) {
        $target = $request->input('target');
    }

    if (! $target) {
        $target = url('/');
    }

    return redirect()->away($this->safeAffiliateTarget($target));
}

private function safeAffiliateTarget(string $target): string
{
    /*
     * لو target رابط داخلي مثل /superbusiness/restaurant
     */
    if (str_starts_with($target, '/')) {
        return url($target);
    }

    /*
     * لو target رابط كامل
     */
    if (filter_var($target, FILTER_VALIDATE_URL)) {
        $allowedHosts = [
            parse_url(config('app.url'), PHP_URL_HOST),
            'ordoraa.com',
            'www.ordoraa.com',
        ];

        $host = parse_url($target, PHP_URL_HOST);

        if (in_array($host, $allowedHosts, true)) {
            return $target;
        }
    }

    return url('/');
}
  public function trackX(Request $request, string $code, \Modules\Affiliate\Services\AffiliateTrackingService $trackingService)
{
    $referral = $trackingService->trackClick($request, $code);

    if (! $referral) {
        return redirect('/');
    }

    $target = $request->input('target', '/');

    if (! str_starts_with($target, '/')) {
        $target = '/';
    }

    return redirect($target);
}
}