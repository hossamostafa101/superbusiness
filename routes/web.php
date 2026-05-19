<?php

use App\Http\Controllers\App\AnalyticsController;
use App\Http\Controllers\App\BookingSettingsController;
use App\Http\Controllers\App\BusinessAppointmentController;
use App\Http\Controllers\App\BusinessCategoryController;
use App\Http\Controllers\App\BusinessCustomerController;
use App\Http\Controllers\App\BusinessLinkController;
use App\Http\Controllers\App\BusinessProductController;
use App\Http\Controllers\App\BusinessProfileController;
use App\Http\Controllers\App\BusinessServiceController;
use App\Http\Controllers\Billing\BillingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Public\RestaurantMenu\PublicRestaurantOrderController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\PublicBusinessPageController;
use App\Http\Controllers\PublicBusinessTrackingController;
use App\Models\Workspace;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/robots.txt', function () {
    $disallow = config('seo.allow_indexing') ? '' : "Disallow: /";
    return response("User-agent: *\n{$disallow}\nSitemap: " . url('/sitemap.xml'), 200)
        ->header('Content-Type', 'text/plain');
});




Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');








Route::get('p/{workspace:slug}/go/whatsapp', [PublicBusinessTrackingController::class, 'whatsapp'])
    ->name('public.business-page.track.whatsapp');

Route::get('p/{workspace:slug}/go/link/{businessLink}', [PublicBusinessTrackingController::class, 'link'])
    ->name('public.business-page.track.link');

Route::get('p/{workspace:slug}/go/product/{businessProduct}/whatsapp', [PublicBusinessTrackingController::class, 'productWhatsapp'])
    ->name('public.business-page.track.product-whatsapp');






Route::get('p/{workspace:slug}/book', [PublicBookingController::class, 'create'])
    ->name('public.booking.create');

Route::post('p/{workspace:slug}/book', [PublicBookingController::class, 'store'])
    ->name('public.booking.store');

Route::get('p/{workspace:slug}/book/success', [PublicBookingController::class, 'success'])
    ->name('public.booking.success');





    Route::post('menu/{workspace:slug}/{branch:slug}/orders', [PublicRestaurantOrderController::class, 'store'])
    ->name('public.restaurant-menu.orders.store');

Route::get('menu/{workspace:slug}/{branch:slug}/orders/{restaurantOrder}/success', [PublicRestaurantOrderController::class, 'success'])
    ->name('public.restaurant-menu.order-success');




    
/*
|--------------------------------------------------------------------------
| Billing Routes - Web Guard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:web', 'workspace.access'])
    ->prefix('billing/{workspace:slug}')
    ->name('billing.')
    ->group(function () {
        Route::get('plans', [BillingController::class, 'plans'])
            ->name('plans');

        Route::get('checkout/{planSlug}', [BillingController::class, 'checkout'])
            ->name('checkout');

        Route::post('checkout/{planSlug}', [BillingController::class, 'process'])
            ->name('process');

        Route::get('success', [BillingController::class, 'success'])
            ->name('success');

        Route::get('cancelled', [BillingController::class, 'cancelled'])
            ->name('cancelled');
    });


Route::get('/onboarding', [OnboardingController::class, 'create'])
    ->name('onboarding.create');

Route::post('/onboarding', [OnboardingController::class, 'store'])
    ->name('onboarding.store');


Route::middleware(['auth:web', 'workspace.access'])
    ->prefix('app/{workspace:slug}')
    ->name('app.')
    ->group(function () {
        Route::get('/', function (Workspace $workspace) {
            return redirect()->route('app.business-profile.edit', $workspace);
        })->name('dashboard');





        Route::get('profile', [BusinessProfileController::class, 'edit'])
            ->name('business-profile.edit');

        Route::put('profile', [BusinessProfileController::class, 'update'])
            ->name('business-profile.update');

        Route::resource('links', BusinessLinkController::class)
            ->except(['show'])
            ->parameters(['links' => 'businessLink']);


        Route::resource('categories', BusinessCategoryController::class)
            ->except(['show'])
            ->parameters([
                'categories' => 'businessCategory',
            ]);

        Route::resource('products', BusinessProductController::class)
            ->except(['show'])
            ->parameters([
                'products' => 'businessProduct',
            ]);




        Route::get('analytics', [AnalyticsController::class, 'index'])
            ->name('analytics.index');



        Route::resource('customers', BusinessCustomerController::class)
            ->except(['show'])
            ->parameters([
                'customers' => 'businessCustomer',
            ]);

        Route::resource('services', BusinessServiceController::class)
            ->except(['show'])
            ->parameters([
                'services' => 'businessService',
            ]);






        Route::get('appointments-calendar', [BusinessAppointmentController::class, 'calendar'])
            ->name('appointments.calendar');

        Route::get('appointments-calendar/events', [BusinessAppointmentController::class, 'calendarEvents'])
            ->name('appointments.calendar-events');



        Route::resource('appointments', BusinessAppointmentController::class)
            ->except(['show'])
            ->parameters([
                'appointments' => 'businessAppointment',
            ]);

        Route::patch('appointments/{businessAppointment}/status', [BusinessAppointmentController::class, 'updateStatus'])
            ->name('appointments.update-status');




        Route::get('booking-settings', [BookingSettingsController::class, 'edit'])
            ->name('booking-settings.edit');

        Route::put('booking-settings', [BookingSettingsController::class, 'update'])
            ->name('booking-settings.update');
    });

Route::get('p/{workspace:slug}', [PublicBusinessPageController::class, 'show'])
    ->name('public.business-page.show');





Route::middleware('auth')->group(function () {});





require __DIR__ . '/admin.php';
require __DIR__ . '/modules/restaurant_menu.php';
require __DIR__ . '/auth.php';
