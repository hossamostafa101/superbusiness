<?php

use Illuminate\Support\Facades\Route;
use Modules\Affiliate\Http\Controllers\Public\AffiliateLandingController;

Route::get('/', [AffiliateLandingController::class, 'index'])
    ->name('landing');

Route::get('/register', [AffiliateLandingController::class, 'register'])
    ->name('register');

Route::post('/register', [AffiliateLandingController::class, 'store'])
    ->name('register.store');

Route::get('/go/{code}', [AffiliateLandingController::class, 'track'])
    ->name('track');