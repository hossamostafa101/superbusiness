<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\App\RestaurantMenu\RestaurantBranchController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantInvoiceController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantItemOptionController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantItemOptionGroupController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantItemVariantController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantMenuCategoryController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantMenuContentSectionController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantMenuDashboardController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantMenuItemController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantMenuOfferController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantMenuSettingsController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantMenuThemeController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantOrderController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantTableController;
use App\Http\Controllers\App\RestaurantMenu\RestaurantTableServiceRequestController;
use App\Http\Controllers\Public\RestaurantMenu\PublicRestaurantInvoiceController;
use App\Http\Controllers\Public\RestaurantMenu\PublicRestaurantMenuController;
use App\Http\Controllers\Public\RestaurantMenu\PublicRestaurantOrderController;
use App\Http\Controllers\Public\RestaurantMenu\PublicRestaurantTableServiceRequestController;

Route::middleware(['auth:web', 'workspace.access', 'workspace.specification:restaurant'])
    ->prefix('superbusiness/app/{workspace:slug}/restaurant-menu')
    ->name('app.restaurant-menu.')
    ->group(function () {
        Route::get('/', function (\App\Models\Workspace $workspace) {
            return redirect()->route('app.restaurant-menu.branches.index', $workspace);
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