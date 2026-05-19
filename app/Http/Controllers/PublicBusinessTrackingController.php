<?php

namespace App\Http\Controllers;

use App\Models\BusinessLink;
use App\Models\BusinessProduct;
use App\Models\Workspace;
use App\Services\Public\BusinessEventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PublicBusinessTrackingController extends Controller
{
    public function __construct(
        private readonly BusinessEventService $businessEventService
    ) {}

    public function whatsapp(Request $request, Workspace $workspace)
    {
        $profile = $workspace->businessProfile;

        abort_if(! $profile || ! $profile->whatsapp_number, 404);

        $number = preg_replace('/\D+/', '', $profile->whatsapp_number);

        $message = urlencode('مرحبًا، وصلت لصفحتكم وأريد الاستفسار.');
        $url = "https://wa.me/{$number}?text={$message}";

        $this->businessEventService->track(
            request: $request,
            workspace: $workspace,
            eventType: 'click_whatsapp',
            targetUrl: $url
        );

        return redirect()->away($url)
            ->withCookie($this->visitorCookie($request));
    }

    public function link(Request $request, Workspace $workspace, BusinessLink $businessLink)
    {
        abort_if((int) $businessLink->workspace_id !== (int) $workspace->id, 404);
        abort_if(! $businessLink->is_active, 404);

        $this->businessEventService->track(
            request: $request,
            workspace: $workspace,
            eventType: 'click_link',
            targetUrl: $businessLink->url,
            businessLinkId: $businessLink->id,
            metadata: [
                'title' => $businessLink->title,
                'icon' => $businessLink->icon,
            ]
        );

        return redirect()->away($businessLink->url)
            ->withCookie($this->visitorCookie($request));
    }

    public function productWhatsapp(Request $request, Workspace $workspace, BusinessProduct $businessProduct)
    {
        abort_if((int) $businessProduct->workspace_id !== (int) $workspace->id, 404);
        abort_if(! $businessProduct->is_available, 404);

        $profile = $workspace->businessProfile;

        abort_if(! $profile || ! $profile->whatsapp_number, 404);

        $number = preg_replace('/\D+/', '', $profile->whatsapp_number);

        $price = $businessProduct->sale_price ?: $businessProduct->price;

        $message = "مرحبًا، أريد الاستفسار عن المنتج: {$businessProduct->name}";

        if ($price) {
            $message .= " - السعر: {$price} {$businessProduct->currency}";
        }

        $url = "https://wa.me/{$number}?text=" . urlencode($message);

        $this->businessEventService->track(
            request: $request,
            workspace: $workspace,
            eventType: 'click_product_whatsapp',
            targetUrl: $url,
            businessProductId: $businessProduct->id,
            metadata: [
                'product_name' => $businessProduct->name,
                'price' => $price,
                'currency' => $businessProduct->currency,
            ]
        );

        return redirect()->away($url)
            ->withCookie($this->visitorCookie($request));
    }

    private function visitorCookie(Request $request)
    {
        $visitorId = $request->cookie('visitor_id') ?: (string) \Illuminate\Support\Str::uuid();

        return Cookie::make(
            name: 'visitor_id',
            value: $visitorId,
            minutes: 60 * 24 * 365,
            path: '/',
            domain: null,
            secure: $request->isSecure(),
            httpOnly: true,
            raw: false,
            sameSite: 'Lax'
        );
    }
}