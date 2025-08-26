<?php

namespace Modules\Packages\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Package\Http\Requests\StoreRequest;
use Modules\Package\Models\Package;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = $request->user();
        $query = Package::query();

        if ($currentUser->role->name === 'sender') {
            $query->where('sender_id', $currentUser->id);
        }

        $packages = $query->orderByDesc('created_at')->paginate($request->integer('per_page', 15));

        return api_success($packages->items(), ['pagination' => [
            'current_page'  => $packages->currentPage(),
            'per_page'      => $packages->perPage(),
            'total'         => $packages->total()
        ]]);
    }

    public function store(StoreRequest $request)
    {
        $this->authorize('create', Package::class);

        $package = Package::create([
            'id'                    => (string) Str::uuid(),
            'sender_id'             => $request->user()->id,
            'tracking_code'         => (string) Str::uuid(),
            'origin_city'           => $request->safe()->origin_city,
            'origin_address'        => $request->safe()->origin_address,
            'destination_city'      => $request->safe()->destination_city,
            'destination_address'   => $request->safe()->destination_address,
            'weight_grams'          => $request->safe()->weight_grams,
        ]);

        return api_success($package, null, 201);
    }

    public function show(Package $package)
    {
        $this->authorize('view', $package);
        return api_success($package);
    }

    public function markInTransit(Request $request, Package $package)
    {
        $this->authorize('markInTransit', $package);

        if ($package->status !== 'created' || is_null($package->carrier_id)) {
            return api_error('invalid_state', 'Package must be accepted by a carrier first.', 422);
        }

        $package->status = 'in_transit';
        $package->save();

        return api_success($package);
    }

    public function markDelivered(Request $request, Package $package)
    {
        $this->authorize('markDelivered', $package);

        if ($package->status !== 'in_transit') {
            return api_error('invalid_state', 'Package is not in transit.', 422);
        }

        $package->status = 'delivered';
        $package->save();

        return api_success($package);
    }

    public function cancel(Request $request, Package $package)
    {
        $this->authorize('cancel', $package);

        if ($package->status !== 'created') {
            return api_error('invalid_state', 'Only packages in created state can be cancelled.', 422);
        }

        $package->status = 'cancelled';
        $package->save();

        return api_success($package);
    }
}
