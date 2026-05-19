<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Workspace $workspace)
    {
        $stats = [
            'total_events' => $workspace->businessEvents()->count(),
            'whatsapp_clicks' => $workspace->businessEvents()
                ->where('event_type', 'click_whatsapp')
                ->count(),
            'product_whatsapp_clicks' => $workspace->businessEvents()
                ->where('event_type', 'click_product_whatsapp')
                ->count(),
            'link_clicks' => $workspace->businessEvents()
                ->where('event_type', 'click_link')
                ->count(),
        ];

        $topProducts = $workspace->businessEvents()
            ->select('business_product_id', DB::raw('COUNT(*) as clicks'))
            ->where('event_type', 'click_product_whatsapp')
            ->whereNotNull('business_product_id')
            ->with('businessProduct:id,name')
            ->groupBy('business_product_id')
            ->orderByDesc('clicks')
            ->limit(10)
            ->get();

        $topLinks = $workspace->businessEvents()
            ->select('business_link_id', DB::raw('COUNT(*) as clicks'))
            ->where('event_type', 'click_link')
            ->whereNotNull('business_link_id')
            ->with('businessLink:id,title')
            ->groupBy('business_link_id')
            ->orderByDesc('clicks')
            ->limit(10)
            ->get();

        return view('app.analytics.index', compact(
            'workspace',
            'stats',
            'topProducts',
            'topLinks'
        ));
    }
}