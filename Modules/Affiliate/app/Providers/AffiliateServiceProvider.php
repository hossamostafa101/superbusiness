<?php

namespace Modules\Affiliate\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AffiliateServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadViewsFrom(
            base_path('Modules/Affiliate/resources/views'),
            'affiliate'
        );

        $this->loadRoutes();
    }

    private function loadRoutes(): void
    {
        Route::middleware(['web'])
            ->as('public.affiliate.')
            ->prefix('affiliate')
            ->group(base_path('Modules/Affiliate/routes/public.php'));

        Route::middleware(['web', 'auth'])
            ->as('affiliate.')
            ->prefix('affiliate/dashboard')
            ->group(base_path('Modules/Affiliate/routes/dashboard.php'));

        Route::middleware(['web', 'auth'])
            ->as('admin.affiliate.')
            ->prefix('admin/affiliate')
            ->group(base_path('Modules/Affiliate/routes/admin.php'));
    }
}