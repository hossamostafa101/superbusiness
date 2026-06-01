<?php

use Illuminate\Support\Facades\Route;
use Modules\Affiliate\Http\Controllers\Admin\AffiliateAdminDashboardController;
use Modules\Affiliate\Http\Controllers\Admin\AffiliateAdminProfileController;
use Modules\Affiliate\Http\Controllers\Admin\AffiliateAdminResourceController;
use Modules\Affiliate\Http\Controllers\Admin\AffiliateAdminSettingController;
use Modules\Affiliate\Http\Controllers\Admin\AffiliateAdminWithdrawalController;

Route::get('/', [AffiliateAdminDashboardController::class, 'index'])
    ->name('dashboard');

Route::get('settings', [AffiliateAdminSettingController::class, 'edit'])
    ->name('settings.edit');

Route::put('settings', [AffiliateAdminSettingController::class, 'update'])
    ->name('settings.update');

Route::get('profiles', [AffiliateAdminProfileController::class, 'index'])
    ->name('profiles.index');

Route::get('profiles/{profile}', [AffiliateAdminProfileController::class, 'show'])
    ->name('profiles.show');

Route::patch('profiles/{profile}/status', [AffiliateAdminProfileController::class, 'updateStatus'])
    ->name('profiles.update-status');

Route::post('profiles/{profile}/manual-commission', [AffiliateAdminProfileController::class, 'storeManualCommission'])
    ->name('profiles.manual-commission');


    Route::post('profiles/{profile}/generate-links', [AffiliateAdminProfileController::class, 'generateLinks'])
    ->name('profiles.generate-links');

    
    Route::resource('resources', AffiliateAdminResourceController::class)
    ->except(['show']);
    
Route::get('withdrawals', [AffiliateAdminWithdrawalController::class, 'index'])
    ->name('withdrawals.index');

Route::get('withdrawals/{withdrawal}', [AffiliateAdminWithdrawalController::class, 'show'])
    ->name('withdrawals.show');

Route::patch('withdrawals/{withdrawal}/approve', [AffiliateAdminWithdrawalController::class, 'approve'])
    ->name('withdrawals.approve');

Route::patch('withdrawals/{withdrawal}/paid', [AffiliateAdminWithdrawalController::class, 'markPaid'])
    ->name('withdrawals.paid');

Route::patch('withdrawals/{withdrawal}/reject', [AffiliateAdminWithdrawalController::class, 'reject'])
    ->name('withdrawals.reject');