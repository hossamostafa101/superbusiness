<?php

namespace Modules\Affiliate\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Affiliate\Models\AffiliateResource;

class AffiliateResourceController extends Controller
{
    public function index(Request $request)
    {
        $resources = AffiliateResource::query()
            ->with('specification')
            ->where('is_active', true)
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->when($request->filled('specification_id'), function ($query) use ($request) {
                $query->where('specification_id', $request->input('specification_id'));
            })
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('affiliate::dashboard.resources.index', compact(
            'resources'
        ));
    }
}