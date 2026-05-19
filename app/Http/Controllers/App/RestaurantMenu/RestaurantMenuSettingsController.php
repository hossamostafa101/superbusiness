<?php

namespace App\Http\Controllers\App\RestaurantMenu;

use App\Http\Controllers\Controller;
use App\Http\Requests\App\RestaurantMenu\UpdateRestaurantMenuSettingsRequest;
use App\Models\Workspace;
use App\Services\App\RestaurantMenu\RestaurantMenuSettingsService;
use App\Services\Core\FeatureLimitService;

class RestaurantMenuSettingsController extends Controller
{
    public function __construct(
        private readonly RestaurantMenuSettingsService $restaurantMenuSettingsService,
        private readonly FeatureLimitService $featureLimitService
    ) {}

    public function edit(Workspace $workspace)
    {
        $settings = $this->restaurantMenuSettingsService->values($workspace);

        $openInvoiceEnabled = $this->featureLimitService->enabled(
            workspace: $workspace,
            featureKey: 'restaurant_open_invoice_enabled'
        );

        $joinWithPinEnabled = $this->featureLimitService->enabled(
            workspace: $workspace,
            featureKey: 'restaurant_invoice_join_with_pin_enabled'
        );

        $invoiceDurationLimit = $this->featureLimitService->limit(
            workspace: $workspace,
            featureKey: 'restaurant_invoice_duration_limit',
            default: 0
        );

        return view('app.restaurant-menu.settings.edit', compact(
            'workspace',
            'settings',
            'openInvoiceEnabled',
            'joinWithPinEnabled',
            'invoiceDurationLimit'
        ));
    }

    public function update(UpdateRestaurantMenuSettingsRequest $request, Workspace $workspace)
    {
        $data = $request->validated();

        if (
            $data['restaurant_ordering_mode'] === 'open_invoice'
            && ! $this->featureLimitService->enabled($workspace, 'restaurant_open_invoice_enabled')
        ) {
            return back()
                ->withInput()
                ->with('error', 'ميزة الفواتير المفتوحة غير متاحة في باقتك الحالية.');
        }

        $durationLimit = $this->featureLimitService->limit(
            workspace: $workspace,
            featureKey: 'restaurant_invoice_duration_limit',
            default: 0
        );

        if ($durationLimit !== -1 && (int) $data['restaurant_invoice_duration_minutes'] > $durationLimit) {
            return back()
                ->withInput()
                ->with('error', 'مدة الفاتورة تتجاوز الحد المسموح في باقتك الحالية.');
        }

        if (
            $data['restaurant_invoice_join_policy'] === 'allow_with_pin'
            && ! $this->featureLimitService->enabled($workspace, 'restaurant_invoice_join_with_pin_enabled')
        ) {
            return back()
                ->withInput()
                ->with('error', 'الانضمام للفاتورة باستخدام PIN غير متاح في باقتك الحالية.');
        }

        $this->restaurantMenuSettingsService->update(
            workspace: $workspace,
            data: $data
        );

        return redirect()
            ->route('app.restaurant-menu.settings.edit', $workspace)
            ->with('success', 'تم تحديث إعدادات المنيو بنجاح.');
    }
}