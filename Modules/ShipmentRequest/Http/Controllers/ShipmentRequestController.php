<?php

namespace Modules\ShipmentRequests\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Package\Models\Package;
use Modules\ShipmentRequest\Enums\ShipmentRequestStatus;
use Modules\ShipmentRequest\Events\ShipmentRequestAccepted;
use Modules\ShipmentRequest\Models\ShipmentRequest;

class ShipmentRequestController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = $request->user();
        $query = ShipmentRequest::query()->with('package');

        if ($currentUser->role->name === 'carrier') {

            $query->where('carrier_id', $currentUser->id);
        } elseif ($currentUser->role->name === 'sender') {

            $query->whereHas('package', fn($q) => $q->where('sender_id', $currentUser->id));
        }

        $requests = $query->orderByDesc('created_at')->paginate($request->integer('per_page', 15));

        return api_success($requests->items(), ['pagination' => [
            'current_page' => $requests->currentPage(),
            'per_page'     => $requests->perPage(),
            'total'        => $requests->total()
        ]]);
    }

    public function store(Request $request, Package $package)
    {
        $this->authorize('create', [ShipmentRequest::class, $package]);

        $exists = ShipmentRequest::where('package_id', $package->id)
            ->where('carrier_id', $request->user()->id)
            ->exists();

        if ($exists) {
            return api_error('duplicate_request', 'Carrier already requested for this package.', 422);
        }

        $shipmentRequest = ShipmentRequest::create([
            'id'            => (string) Str::uuid(),
            'package_id'    => $package->id,
            'carrier_id'    => $request->user()->id,
            'status'        => ShipmentRequestStatus::PENDING->value,
        ]);

        return api_success($shipmentRequest, null, 201);
    }

    public function approve(Request $request, ShipmentRequest $requestModel)
    {
        $requestModel->load('package');
        $this->authorize('approve', $requestModel);

        if ($requestModel->status !== ShipmentRequestStatus::PENDING->value) {
            return api_error('invalid_state', 'Only pending requests can be approved.', 422);
        }

        $alreadyAccepted = ShipmentRequest::where('package_id', $requestModel->package_id)
            ->where('status', ShipmentRequestStatus::ACCEPTED->value)->exists();

        if ($alreadyAccepted) {
            return api_error('already_accepted', 'Package already has an accepted request.', 422);
        }

        $requestModel->status = ShipmentRequestStatus::ACCEPTED->value;
        $requestModel->save();

        event(new ShipmentRequestAccepted($requestModel));

        return api_success(['id' => $requestModel->id, 'status' => $requestModel->status]);
    }
}
