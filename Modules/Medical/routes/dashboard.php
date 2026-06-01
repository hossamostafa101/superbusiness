<?php

use Illuminate\Support\Facades\Route;
use Modules\Medical\Http\Controllers\Dashboard\MedicalAppointmentBoardController;
use Modules\Medical\Http\Controllers\Dashboard\MedicalAppointmentController;
use Modules\Medical\Http\Controllers\Dashboard\MedicalDashboardController;
use Modules\Medical\Http\Controllers\Dashboard\MedicalBranchController;
use Modules\Medical\Http\Controllers\Dashboard\MedicalDepartmentController;
use Modules\Medical\Http\Controllers\Dashboard\MedicalPatientController;
use Modules\Medical\Http\Controllers\Dashboard\MedicalPrescriptionController;
use Modules\Medical\Http\Controllers\Dashboard\MedicalServiceController;
use Modules\Medical\Http\Controllers\Dashboard\MedicalSettingController;
use Modules\Medical\Http\Controllers\Dashboard\MedicalSpecialtyController;
use Modules\Medical\Http\Controllers\Dashboard\MedicalStaffController;
use Modules\Medical\Http\Controllers\Dashboard\MedicalStaffServiceController;
use Modules\Medical\Http\Controllers\Dashboard\MedicalStaffWorkingHourController;
use Modules\Medical\Http\Controllers\Dashboard\MedicalVisitController;
use Modules\Medical\Http\Controllers\Dashboard\MedicalVisitNoteController;

Route::get('/', [MedicalDashboardController::class, 'index'])
    ->name('dashboard');

    Route::get('settings', [MedicalSettingController::class, 'edit'])
    ->name('settings.edit');

Route::put('settings', [MedicalSettingController::class, 'update'])
    ->name('settings.update');
    
Route::resource('branches', MedicalBranchController::class)
    ->except(['show']);


    Route::resource('departments', MedicalDepartmentController::class)
    ->except(['show']);

Route::resource('specialties', MedicalSpecialtyController::class)
    ->except(['show']);


    Route::resource('services', MedicalServiceController::class)
    ->except(['show']);


    Route::resource('staff', MedicalStaffController::class)
    ->except(['show']);





    Route::get('staff/{staff}/services', [MedicalStaffServiceController::class, 'edit'])
    ->name('staff.services.edit');

Route::put('staff/{staff}/services', [MedicalStaffServiceController::class, 'update'])
    ->name('staff.services.update');






    Route::resource('visits', MedicalVisitController::class);

Route::post('visits/{visit}/notes', [MedicalVisitNoteController::class, 'store'])
    ->name('visits.notes.store');

Route::delete('visits/{visit}/notes/{note}', [MedicalVisitNoteController::class, 'destroy'])
    ->name('visits.notes.destroy');

Route::post('appointments/{appointment}/start-visit', [MedicalVisitController::class, 'startFromAppointment'])
    ->name('appointments.start-visit');






    


    Route::get('staff/{staff}/working-hours', [MedicalStaffWorkingHourController::class, 'edit'])
    ->name('staff.working-hours.edit');

Route::put('staff/{staff}/working-hours', [MedicalStaffWorkingHourController::class, 'update'])
    ->name('staff.working-hours.update');




    Route::resource('patients', MedicalPatientController::class);


Route::get('appointments-board', [MedicalAppointmentBoardController::class, 'index'])
    ->name('appointments.board');

Route::patch('appointments-board/{appointment}/status', [MedicalAppointmentBoardController::class, 'updateStatus'])
    ->name('appointments.board.update-status');

    Route::get('appointments/available-slots', [MedicalAppointmentController::class, 'availableSlots'])
    ->name('appointments.available-slots');

    Route::resource('appointments', MedicalAppointmentController::class);

Route::patch('appointments/{appointment}/status', [MedicalAppointmentController::class, 'updateStatus'])
    ->name('appointments.update-status');







    Route::resource('prescriptions', MedicalPrescriptionController::class);

Route::get('prescriptions/{prescription}/print', [MedicalPrescriptionController::class, 'print'])
    ->name('prescriptions.print');