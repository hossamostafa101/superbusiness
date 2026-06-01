<?php

use Illuminate\Support\Facades\Route;
use Modules\Medical\Http\Controllers\Public\MedicalPublicBookingController;
use Modules\Medical\Http\Controllers\Public\MedicalPublicController;

Route::get('{workspace:slug}', [MedicalPublicController::class, 'show'])
    ->name('show');

Route::get('{workspace:slug}/book', [MedicalPublicBookingController::class, 'create'])
    ->name('booking.create');


    Route::get('{workspace:slug}/book/staff-by-service', [MedicalPublicBookingController::class, 'staffByService'])
    ->name('booking.staff-by-service');
    
Route::get('{workspace:slug}/book/available-slots', [MedicalPublicBookingController::class, 'availableSlots'])
    ->name('booking.available-slots');

Route::post('{workspace:slug}/book', [MedicalPublicBookingController::class, 'store'])
    ->name('booking.store');

Route::get('{workspace:slug}/book/success/{appointment}', [MedicalPublicBookingController::class, 'success'])
    ->name('booking.success');