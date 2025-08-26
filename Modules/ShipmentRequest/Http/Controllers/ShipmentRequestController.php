<?php

namespace Modules\ShipmentRequest\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Package\Models\Package;
use Modules\ShipmentRequest\Enums\ShipmentRequestStatus;
use Modules\ShipmentRequest\Events\ShipmentRequestAccepted;
use Modules\ShipmentRequest\Models\ShipmentRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *   name="ShipmentRequests",
 *   description="Operations for carriers to request & send shipments"
 * )
 */
class ShipmentRequestController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/v1/requests",
     *   tags={"ShipmentRequests"},
     *   summary="List shipment requests (role-aware)",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="page", in="query", @OA\Schema(type="integer", example=1)),
     *   @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", example=15)),
     *   @OA\Response(
     *     response=200, description="List of requests",
     *     @OA\JsonContent(
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ShipmentRequest")),
     *       @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta"),
     *       @OA\Property(property="errors", type="null")
     *     )
     *   ),
     *   @OA\Response(response=401, description="Unauthenticated")
     * )
     */
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

    /**
     * @OA\Post(
     *   path="/api/v1/packages/{package_id}/requests",
     *   tags={"ShipmentRequests"},
     *   summary="Create shipment request for a package (carrier only)",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="package_id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *   @OA\Response(
     *     response=201, description="Created",
     *     @OA\JsonContent(
     *       @OA\Property(property="data", ref="#/components/schemas/ShipmentRequest"),
     *       @OA\Property(property="meta", type="null"),
     *       @OA\Property(property="errors", type="null")
     *     )
     *   ),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=409, description="Duplicate request", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope_Object"))
     * )
     */
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

    /**
     * @OA\Post(
     *   path="/api/v1/requests/{request_id}/approve",
     *   tags={"ShipmentRequests"},
     *   summary="Approve a shipment request (only package owner)",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="request_id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *   @OA\Response(
     *     response=200, description="Approved",
     *     @OA\JsonContent(
     *       @OA\Property(property="data", type="object",
     *         @OA\Property(property="id", type="string", format="uuid"),
     *         @OA\Property(property="status", type="string", example="accepted")
     *       ),
     *       @OA\Property(property="meta", type="null"),
     *       @OA\Property(property="errors", type="null")
     *     )
     *   ),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=422, description="Invalid state or already accepted", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope_Object"))
     * )
     */
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
