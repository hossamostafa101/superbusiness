<?php

namespace App\Services\Public;

use App\Models\BusinessEvent;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessEventService
{
    public function track(
        Request $request,
        Workspace $workspace,
        string $eventType,
        ?string $targetUrl = null,
        ?int $businessLinkId = null,
        ?int $businessProductId = null,
        array $metadata = []
    ): BusinessEvent {
        $visitorId = $request->cookie('visitor_id');

        if (! $visitorId) {
            $visitorId = (string) Str::uuid();
        }

        return BusinessEvent::create([
            'workspace_id' => $workspace->id,
            'event_type' => $eventType,
            'business_link_id' => $businessLinkId,
            'business_product_id' => $businessProductId,
            'visitor_id' => $visitorId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->headers->get('referer'),
            'target_url' => $targetUrl,
            'metadata' => $metadata,
        ]);
    }
}