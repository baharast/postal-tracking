<?php

namespace Modules\ShipmentRequest\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Package\Models\Package;
use Modules\ShipmentRequest\Models\ShipmentRequest;

class ShipmentRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    public function create(User $user, Package $package): bool
    {
        return $user->role->name === 'carrier';
    }

    public function approve(User $user, ShipmentRequest $shipmentRequest): bool
    {
        return $user->role->name === 'sender' && $shipmentRequest->package->sender_id === $user->id;
    }

    public function view(User $user, ShipmentRequest $shipmentRequest): bool
    {
        return $user->role->name === 'admin'
            || $shipmentRequest->carrier_id === $user->id
            || $shipmentRequest->package->sender_id === $user->id;
    }
}
