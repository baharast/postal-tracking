<?php

use Illuminate\Support\Facades\Route;
use Modules\ShipmentRequests\Http\Controllers\ShipmentRequestController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('requests', [ShipmentRequestController::class, 'index']);
    Route::post('packages/{package}/requests', [ShipmentRequestController::class, 'store']);
    Route::post('requests/{request}/approve', [ShipmentRequestController::class, 'approve']);
});
