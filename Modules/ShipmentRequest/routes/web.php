<?php

use Illuminate\Support\Facades\Route;
use Modules\ShipmentRequest\Http\Controllers\ShipmentRequestController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('shipmentrequests', ShipmentRequestController::class)->names('shipmentrequest');
});
