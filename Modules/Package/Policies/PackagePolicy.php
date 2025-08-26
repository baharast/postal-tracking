<?php

namespace Modules\Package\Policies;

use Modules\User\Models\User;
use Modules\Package\Models\Package;

class PackagePolicy
{
    public function view(User $user, Package $package): bool
    {
        return $user->role->name === 'admin'
            || $package->sender_id === $user->id
            || ($user->role->name === 'carrier' && $package->carrier_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->role->name === 'sender' || $user->role->name === 'admin';
    }

    public function markInTransit(User $user, Package $package): bool
    {
        return $user->role->name === 'sender' && $package->sender_id === $user->id;
    }

    public function markDelivered(User $user, Package $package): bool
    {
        return $user->role->name === 'carrier' && $package->carrier_id === $user->id;
    }

    public function cancel(User $user, Package $package): bool
    {
        return $user->role->name === 'sender' && $package->sender_id === $user->id;
    }
}
