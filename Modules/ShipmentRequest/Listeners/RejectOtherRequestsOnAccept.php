<?php

namespace Modules\ShipmentRequest\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Package\Enums\PackageStatus;
use Modules\Package\Models\Package;
use Modules\ShipmentRequest\Enums\ShipmentRequestStatus;
use Modules\ShipmentRequest\Events\ShipmentRequestAccepted;
use Modules\ShipmentRequest\Models\ShipmentRequest;

class RejectOtherRequestsOnAccept
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(ShipmentRequestAccepted $event): void
    {
        $acceptedRequest = $event->shipmentRequest;

        ShipmentRequest::where('package_id', $acceptedRequest->package_id)
            ->where('id', '!=', $acceptedRequest->id)
            ->where('status', ShipmentRequestStatus::PENDING->value)
            ->update([
                'status'        => ShipmentRequestStatus::REJECTED->value,
                'reject_reason' => 'auto'
            ]);

        $package = Package::findOrFail($acceptedRequest->package_id);

        $package->carrier_id = $acceptedRequest->carrier_id;

        if ($package->status === PackageStatus::CREATED->value) {
            $package->status = PackageStatus::IN_TRANSIT->value;
        }

        $package->save();
    }
}
