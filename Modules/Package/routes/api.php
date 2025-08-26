<?php

use Illuminate\Support\Facades\Route;
use Modules\Package\Http\Controllers\PackageController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('packages', [PackageController::class, 'index']);
    Route::post('packages', [PackageController::class, 'store']);
    Route::get('packages/{package}', [PackageController::class, 'show']);

    Route::post('packages/{package}/status/in-transit', [PackageController::class, 'markInTransit']);
    Route::post('packages/{package}/status/delivered', [PackageController::class, 'markDelivered']);
    Route::post('packages/{package}/status/cancel', [PackageController::class, 'cancel']);
});
