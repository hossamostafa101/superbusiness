<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\RestaurantMenu\UpdateRestaurantMenuThemeRequest;
use App\Models\Workspace;
use App\Services\App\RestaurantMenu\RestaurantMenuThemeService;
use App\Services\Core\FeatureLimitService;

class RestaurantMenuThemeController extends Controller
{
    public function __construct(
        private readonly RestaurantMenuThemeService $restaurantMenuThemeService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function edit(Workspace $workspace)
    {
        if (! $this->featureLimitService->enabled($workspace, 'restaurant_menu_templates_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.settings.edit', $workspace)
                ->with('error', 'قوالب المنيو غير متاحة في باقتك الحالية.');
        }

        $assignment = $this->restaurantMenuThemeService->assignment($workspace);
        $options = $this->restaurantMenuThemeService->options($workspace);

        $customSectionsEnabled = $this->featureLimitService->enabled(
            workspace: $workspace,
            featureKey: 'restaurant_menu_custom_sections_enabled'
        );

        $customCssEnabled = $this->featureLimitService->enabled(
            workspace: $workspace,
            featureKey: 'restaurant_menu_custom_css_enabled'
        );

        

        $previewBranch = $workspace->restaurantBranches()
    ->where('is_active', true)
    ->orderByDesc('is_default')
    ->orderBy('sort_order')
    ->orderBy('id')
    ->first();

$previewUrl = $previewBranch
    ? route('public.restaurant-menu.branch', [$workspace, $previewBranch])
    : route('public.restaurant-menu.workspace', $workspace);


        return view('app.restaurant-menu.theme.edit', compact(
            'workspace',
            'assignment',
            'options',
            'customSectionsEnabled',
            'customCssEnabled',
            'previewUrl'
        ));
    }

    public function update(UpdateRestaurantMenuThemeRequest $request, Workspace $workspace)
    {
        if (! $this->featureLimitService->enabled($workspace, 'restaurant_menu_templates_enabled')) {
            return redirect()
                ->route('app.restaurant-menu.settings.edit', $workspace)
                ->with('error', 'قوالب المنيو غير متاحة في باقتك الحالية.');
        }

        try {
            $this->restaurantMenuThemeService->update(
                workspace: $workspace,
                data: $request->validated()
            );

            return redirect()
                ->route('app.restaurant-menu.theme.edit', $workspace)
                ->with('success', 'تم تحديث تصميم المنيو بنجاح.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}