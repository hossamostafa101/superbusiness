<?php

use Illuminate\Support\Facades\Route;
use Modules\Affiliate\Http\Controllers\Dashboard\AffiliateCommissionController;
use Modules\Affiliate\Http\Controllers\Dashboard\AffiliateDashboardController;
use Modules\Affiliate\Http\Controllers\Dashboard\AffiliateLinkController;
use Modules\Affiliate\Http\Controllers\Dashboard\AffiliateResourceController;
use Modules\Affiliate\Http\Controllers\Dashboard\AffiliateWithdrawalController;

Route::get('/', [AffiliateDashboardController::class, 'index'])
    ->name('dashboard');

Route::get('links', [AffiliateLinkController::class, 'index'])
    ->name('links.index');

Route::get('commissions', [AffiliateCommissionController::class, 'index'])
    ->name('commissions.index');

Route::get('resources', [AffiliateResourceController::class, 'index'])
    ->name('resources.index');

Route::get('withdrawals', [AffiliateWithdrawalController::class, 'index'])
    ->name('withdrawals.index');

Route::post('withdrawals', [AffiliateWithdrawalController::class, 'store'])
    ->name('withdrawals.store');