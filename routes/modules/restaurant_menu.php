<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\App\RestaurantMenu\RestaurantBranchController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantCashRegisterController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantDeliveryCourierController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantDeliverySettingController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantDeliveryZoneController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantInvoiceController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantItemOptionController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantItemOptionGroupController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantItemVariantController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantMenuCategoryController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantMenuContentSectionController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantMenuDashboardController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantMenuItemController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantMenuOfferController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantMenuPwaSettingController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantMenuSettingsController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantMenuThemeController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantOrderController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantPaymentMethodController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantPosController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantPosCustomerController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantPosSettingController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantPosShiftController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantStaffController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantTableController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantTableServiceRequestController;
use App\Http\Controllers\Public\RestaurantMenu\PublicCustomerAccountController;
use App\Http\Controllers\Public\RestaurantMenu\PublicRestaurantInvoiceController;
use App\Http\Controllers\Public\RestaurantMenu\PublicRestaurantMenuController;
use App\Http\Controllers\Public\RestaurantMenu\PublicRestaurantMenuPwaController;
use App\Http\Controllers\Public\RestaurantMenu\PublicRestaurantOrderController;
use App\Http\Controllers\Public\RestaurantMenu\PublicRestaurantTableServiceRequestController;

Route::middleware(['auth:web', 'workspace.access', 'workspace.specification:restaurant'])
    ->prefix('app/{workspace:slug}/restaurant-menu')
    ->name('app.restaurant-menu.')
    ->group(function () {
        Route::get('/', function (\App\Models\Workspace $workspace) {
            // return redirect()->route('app.restaurant-menu.branches.index', $workspace);
            return redirect()->route('app.restaurant-menu.dashboard', $workspace);
        })->name('dashboardx');

        Route::get('dashboard', RestaurantMenuDashboardController::class)
    ->name('dashboard');

        Route::resource('branches', RestaurantBranchController::class)
            ->except(['show'])
            ->parameters([
                'branches' => 'restaurantBranch',
            ]);

        Route::resource('categories', RestaurantMenuCategoryController::class)
            ->except(['show'])
            ->parameters([
                'categories' => 'restaurantMenuCategory',
            ]);

        Route::resource('items', RestaurantMenuItemController::class)
            ->except(['show'])
            ->parameters([
                'items' => 'restaurantMenuItem',
            ]);


            Route::get('theme', [RestaurantMenuThemeController::class, 'edit'])
    ->name('theme.edit');

Route::put('theme', [RestaurantMenuThemeController::class, 'update'])
    ->name('theme.update');



    Route::resource('content-sections', RestaurantMenuContentSectionController::class)
    ->except(['show'])
    ->parameters([
        'content-sections' => 'contentSection',
    ]);

    Route::get('content-sections/{contentSection}/offers', [RestaurantMenuOfferController::class, 'index'])
    ->name('content-sections.offers.index');

Route::get('content-sections/{contentSection}/offers/create', [RestaurantMenuOfferController::class, 'create'])
    ->name('content-sections.offers.create');

Route::post('content-sections/{contentSection}/offers', [RestaurantMenuOfferController::class, 'store'])
    ->name('content-sections.offers.store');

Route::get('content-sections/{contentSection}/offers/{offer}/edit', [RestaurantMenuOfferController::class, 'edit'])
    ->name('content-sections.offers.edit');

Route::put('content-sections/{contentSection}/offers/{offer}', [RestaurantMenuOfferController::class, 'update'])
    ->name('content-sections.offers.update');

Route::delete('content-sections/{contentSection}/offers/{offer}', [RestaurantMenuOfferController::class, 'destroy'])
    ->name('content-sections.offers.destroy');


    
        Route::prefix('items/{restaurantMenuItem}')
            ->name('items.')
            ->group(function () {
                Route::resource('variants', RestaurantItemVariantController::class)
                    ->except(['show'])
                    ->parameters([
                        'variants' => 'restaurantItemVariant',
                    ]);

                Route::resource('option-groups', RestaurantItemOptionGroupController::class)
                    ->except(['show'])
                    ->parameters([
                        'option-groups' => 'restaurantItemOptionGroup',
                    ]);
            });

        Route::prefix('items/{restaurantMenuItem}/option-groups/{restaurantItemOptionGroup}')
            ->name('items.option-groups.')
            ->group(function () {
                Route::resource('options', RestaurantItemOptionController::class)
                    ->except(['show'])
                    ->parameters([
                        'options' => 'restaurantItemOption',
                    ]);
            });



            


            Route::get('orders/{restaurantOrder}/edit', [RestaurantOrderController::class, 'edit'])
    ->name('orders.edit');

Route::put('orders/{restaurantOrder}', [RestaurantOrderController::class, 'update'])
    ->name('orders.update');


    Route::put('orders/{restaurantOrder}/items', [RestaurantOrderController::class, 'updateItems'])
    ->name('orders.update-items');

Route::post('orders/{restaurantOrder}/items', [RestaurantOrderController::class, 'storeItem'])
    ->name('orders.items.store');
    

Route::patch('orders/{restaurantOrder}/cancel', [RestaurantOrderController::class, 'cancel'])
    ->name('orders.cancel');



    

            Route::patch('orders/{restaurantOrder}/delivery', [RestaurantOrderController::class, 'updateDelivery'])
    ->name('orders.update-delivery');
    
        Route::get('orders', [RestaurantOrderController::class, 'index'])
            ->name('orders.index');



            Route::get('orders/live', [RestaurantOrderController::class, 'live'])
    ->name('orders.live');



        Route::get('orders/{restaurantOrder}', [RestaurantOrderController::class, 'show'])
            ->name('orders.show');

        Route::patch('orders/{restaurantOrder}/status', [RestaurantOrderController::class, 'updateStatus'])
            ->name('orders.update-status');


            Route::get('orders/{restaurantOrder}/receipt', [RestaurantOrderController::class, 'receipt'])
    ->name('orders.receipt');
    



            Route::get('service-requests', [RestaurantTableServiceRequestController::class, 'index'])
    ->name('service-requests.index');

Route::patch('service-requests/{serviceRequest}/status', [RestaurantTableServiceRequestController::class, 'updateStatus'])
    ->name('service-requests.update-status');




        Route::get('tables/print', [RestaurantTableController::class, 'printAll'])
            ->name('tables.print-all');

        Route::get('tables/{restaurantTable}/print', [RestaurantTableController::class, 'printOne'])
            ->name('tables.print-one');



        Route::resource('tables', RestaurantTableController::class)
            ->except(['show'])
            ->parameters([
                'tables' => 'restaurantTable',
            ]);

        Route::patch('tables/{restaurantTable}/regenerate-code', [RestaurantTableController::class, 'regenerateCode'])
            ->name('tables.regenerate-code');







            Route::get('invoices', [RestaurantInvoiceController::class, 'index'])
    ->name('invoices.index');

Route::get('invoices/{restaurantInvoice}', [RestaurantInvoiceController::class, 'show'])
    ->name('invoices.show');

Route::patch('invoices/{restaurantInvoice}/status', [RestaurantInvoiceController::class, 'updateStatus'])
    ->name('invoices.update-status');

Route::patch('invoices/{restaurantInvoice}/extend', [RestaurantInvoiceController::class, 'extend'])
    ->name('invoices.extend');

Route::patch('invoices/{restaurantInvoice}/recalculate', [RestaurantInvoiceController::class, 'recalculate'])
    ->name('invoices.recalculate');

    

    Route::get('settings', [RestaurantMenuSettingsController::class, 'edit'])
    ->name('settings.edit');

Route::put('settings', [RestaurantMenuSettingsController::class, 'update'])
    ->name('settings.update');








    Route::resource('payment-methods', RestaurantPaymentMethodController::class)
    ->except(['show'])
    ->names('payment-methods');

Route::resource('staff', RestaurantStaffController::class)
    ->except(['show'])
    ->names('staff');

Route::get('pos-settings', [RestaurantPosSettingController::class, 'edit'])
    ->name('pos-settings.edit');

Route::put('pos-settings', [RestaurantPosSettingController::class, 'update'])
    ->name('pos-settings.update');

    





    Route::resource('cash-registers', RestaurantCashRegisterController::class)
    ->except(['show'])
    ->names('cash-registers');

Route::get('pos-shifts', [RestaurantPosShiftController::class, 'index'])
    ->name('pos-shifts.index');

Route::get('pos-shifts/create', [RestaurantPosShiftController::class, 'create'])
    ->name('pos-shifts.create');

Route::post('pos-shifts', [RestaurantPosShiftController::class, 'store'])
    ->name('pos-shifts.store');

Route::get('pos-shifts/{shift}', [RestaurantPosShiftController::class, 'show'])
    ->name('pos-shifts.show');

Route::patch('pos-shifts/{shift}/close', [RestaurantPosShiftController::class, 'close'])
    ->name('pos-shifts.close');



Route::get('pos', [RestaurantPosController::class, 'index'])
    ->name('pos.index');

Route::post('pos/orders', [RestaurantPosController::class, 'store'])
    ->name('pos.orders.store');





    Route::get('pwa-settings', [RestaurantMenuPwaSettingController::class, 'edit'])
    ->name('pwa-settings.edit');

Route::put('pwa-settings', [RestaurantMenuPwaSettingController::class, 'update'])
    ->name('pwa-settings.update');












    Route::resource('delivery-zones', RestaurantDeliveryZoneController::class)
    ->except(['show'])
    ->names('delivery-zones');

Route::resource('delivery-couriers', RestaurantDeliveryCourierController::class)
    ->except(['show'])
    ->names('delivery-couriers');

Route::get('delivery-settings', [RestaurantDeliverySettingController::class, 'edit'])
    ->name('delivery-settings.edit');

Route::put('delivery-settings', [RestaurantDeliverySettingController::class, 'update'])
    ->name('delivery-settings.update');









    Route::get('pos/customers/search', [RestaurantPosCustomerController::class, 'search'])
    ->name('pos.customers.search');

Route::get('pos/customers/{customer}', [RestaurantPosCustomerController::class, 'show'])
    ->name('pos.customers.show');
    

    
    });







Route::get('menu/{workspace:slug}', [PublicRestaurantMenuController::class, 'showWorkspace'])
    ->name('public.restaurant-menu.workspace');

Route::get('menu/{workspace:slug}/{branch:slug}', [PublicRestaurantMenuController::class, 'showBranch'])
    ->name('public.restaurant-menu.branch');


Route::post('menu/{workspace:slug}/{branch:slug}/invoices/open', [PublicRestaurantInvoiceController::class, 'open'])
    ->name('public.restaurant-menu.invoices.open');

Route::post('menu/{workspace:slug}/{branch:slug}/invoices/{restaurantInvoice}/join', [PublicRestaurantInvoiceController::class, 'join'])
    ->name('public.restaurant-menu.invoices.join');

Route::get('menu/{workspace:slug}/{branch:slug}/invoices/{restaurantInvoice}', [PublicRestaurantInvoiceController::class, 'show'])
    ->name('public.restaurant-menu.invoices.show');


    Route::post('menu/{workspace:slug}/{branch:slug}/service-request', [PublicRestaurantTableServiceRequestController::class, 'store'])
    ->name('public.restaurant-menu.service-request.store');




    Route::get('menu/{workspace:slug}/{branch:slug}/orders/{restaurantOrder}/track', [PublicRestaurantOrderController::class, 'track'])
    ->name('public.restaurant-menu.orders.track');

Route::get('menu/{workspace:slug}/{branch:slug}/orders/{restaurantOrder}/status', [PublicRestaurantOrderController::class, 'status'])
    ->name('public.restaurant-menu.orders.status');





    Route::get('menu/{workspace:slug}/{branch:slug}/offers', [PublicRestaurantMenuController::class, 'offers'])
    ->name('public.restaurant-menu.offers');






    Route::get('menu/{workspace:slug}/{branch:slug}/account', [PublicCustomerAccountController::class, 'dashboard'])
    ->name('public.restaurant-menu.customer.dashboard');

Route::get('menu/{workspace:slug}/{branch:slug}/login', [PublicCustomerAccountController::class, 'loginForm'])
    ->name('public.restaurant-menu.customer.login');

Route::post('menu/{workspace:slug}/{branch:slug}/login', [PublicCustomerAccountController::class, 'login'])
    ->name('public.restaurant-menu.customer.login.submit');

Route::get('menu/{workspace:slug}/{branch:slug}/register', [PublicCustomerAccountController::class, 'registerForm'])
    ->name('public.restaurant-menu.customer.register');

Route::post('menu/{workspace:slug}/{branch:slug}/register', [PublicCustomerAccountController::class, 'register'])
    ->name('public.restaurant-menu.customer.register.submit');

Route::post('menu/{workspace:slug}/{branch:slug}/logout', [PublicCustomerAccountController::class, 'logout'])
    ->name('public.restaurant-menu.customer.logout');






Route::get('menu/{workspace:slug}/{branch:slug}/manifest.webmanifest', [PublicRestaurantMenuPwaController::class, 'manifest'])
    ->name('public.restaurant-menu.pwa.manifest');

Route::get('menu/{workspace:slug}/{branch:slug}/offline', [PublicRestaurantMenuPwaController::class, 'offline'])
    ->name('public.restaurant-menu.pwa.offline');