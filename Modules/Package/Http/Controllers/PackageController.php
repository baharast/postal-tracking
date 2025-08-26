<?php

namespace Modules\Package\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Package\Http\Requests\StoreRequest;
use Modules\Package\Models\Package;
use OpenApi\Annotations as OA;

class PackageController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/v1/packages",
     *   tags={"Packages"},
     *   summary="List packages (role-aware)",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="page", in="query", description="Page number", @OA\Schema(type="integer", example=1)),
     *   @OA\Parameter(name="per_page", in="query", description="Items per page", @OA\Schema(type="integer", example=15)),
     *   @OA\Response(
     *     response=200, description="List of packages",
     *     @OA\JsonContent(
     *       @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Package")),
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

    /**
     * @OA\Post(
     *   path="/api/v1/packages",
     *   tags={"Packages"},
     *   summary="Create a new package (sender or admin)",
     *   security={{"bearerAuth": {}}},
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/PackageCreateRequest")),
     *   @OA\Response(
     *     response=201, description="Created",
     *     @OA\JsonContent(
     *       @OA\Property(property="data", ref="#/components/schemas/Package"),
     *       @OA\Property(property="meta", type="null"),
     *       @OA\Property(property="errors", type="null")
     *     )
     *   ),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope_Object"))
     * )
     */
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

    /**
     * @OA\Get(
     *   path="/api/v1/packages/{package_id}",
     *   tags={"Packages"},
     *   summary="Get details of a package",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="package_id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *   @OA\Response(
     *     response=200, description="Package",
     *     @OA\JsonContent(
     *       @OA\Property(property="data", ref="#/components/schemas/Package"),
     *       @OA\Property(property="meta", type="null"),
     *       @OA\Property(property="errors", type="null")
     *     )
     *   ),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(Package $package)
    {
        $this->authorize('view', $package);
        return api_success($package);
    }

    /**
     * @OA\Post(
     *   path="/api/v1/packages/{package_id}/status/in-transit",
     *   tags={"Packages"},
     *   summary="Mark package as in_transit (only sender owner)",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="package_id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *   @OA\Response(response=200, description="Updated", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope_Object")),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=422, description="Invalid state")
     * )
     */
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

    /**
     * @OA\Post(
     *   path="/api/v1/packages/{package_id}/status/delivered",
     *   tags={"Packages"},
     *   summary="Mark package as delivered (only selected carrier)",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="package_id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *   @OA\Response(response=200, description="Updated", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope_Object")),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=422, description="Invalid state")
     * )
     */
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

    /**
     * @OA\Post(
     *   path="/api/v1/packages/{package_id}/status/cancel",
     *   tags={"Packages"},
     *   summary="Cancel package (only sender, only in 'created')",
     *   security={{"bearerAuth": {}}},
     *   @OA\Parameter(name="package_id", in="path", required=true, @OA\Schema(type="string", format="uuid")),
     *   @OA\Response(response=200, description="Cancelled", @OA\JsonContent(ref="#/components/schemas/ApiEnvelope_Object")),
     *   @OA\Response(response=403, description="Forbidden"),
     *   @OA\Response(response=422, description="Invalid state")
     * )
     */
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
