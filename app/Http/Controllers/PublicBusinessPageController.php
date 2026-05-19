<?php

namespace App\Http\Controllers;

use App\Models\Workspace;

class PublicBusinessPageController extends Controller
{
    public function show(Workspace $workspace)
    {
        abort_if($workspace->status !== 'active', 404);

        $workspace->load([
            'businessProfile',

            'businessLinks' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderByDesc('id');
            },

            'businessCategories' => function ($query) {
                $query->where('is_active', true)
                    ->with(['products' => function ($productQuery) {
                        $productQuery->where('is_available', true)
                            ->orderBy('sort_order')
                            ->orderByDesc('id');
                    }])
                    ->orderBy('sort_order')
                    ->orderBy('name');
            },

            'businessProducts' => function ($query) {
                $query->where('is_available', true)
                    ->whereNull('category_id')
                    ->orderBy('sort_order')
                    ->orderByDesc('id');
            },

            'activeSubscription.plan.features',
        ]);

        $profile = $workspace->businessProfile;

        abort_if(! $profile || ! $profile->is_published, 404);

        return view('public.business-page.show', compact('workspace', 'profile'));
    }
}