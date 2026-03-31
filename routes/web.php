<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleMonthlyLogController;

Route::get('/', function () {
    return redirect()->route('vehicle-logs.index');
});

Route::resource('vehicles', VehicleController::class)->except(['show']);

Route::resource('vehicle-logs', VehicleMonthlyLogController::class)->parameters([
    'vehicle-logs' => 'vehicle_log'
]);

Route::get('vehicle-logs/{vehicle_log}/invoice', [App\Http\Controllers\VehicleMonthlyLogController::class, 'invoice'])
    ->name('vehicle-logs.invoice');

Route::get('vehicle-logs/{vehicle_log}/daily-entry', [VehicleMonthlyLogController::class, 'dailyEntry'])
    ->name('vehicle-logs.daily-entry');

Route::post('vehicle-logs/{vehicle_log}/daily-entry', [VehicleMonthlyLogController::class, 'saveDailyEntry'])
    ->name('vehicle-logs.save-daily-entry');

    Route::get('vehicle-logs/{vehicle_log}/bill', [VehicleMonthlyLogController::class, 'bill'])
    ->name('vehicle-logs.bill');




