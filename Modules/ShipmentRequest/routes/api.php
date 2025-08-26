<?php

use Illuminate\Support\Facades\Route;
use Modules\ShipmentRequest\Http\Controllers\ShipmentRequestController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('shipmentrequests', ShipmentRequestController::class)->names('shipmentrequest');
});
