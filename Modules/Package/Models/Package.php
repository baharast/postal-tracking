<?php

namespace Modules\Package\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="Package",
 *   required={"id","sender_id","tracking_code","status","origin_city","destination_city"},
 *   @OA\Property(property="id", type="string", format="uuid"),
 *   @OA\Property(property="sender_id", type="string", format="uuid"),
 *   @OA\Property(property="carrier_id", type="string", format="uuid", nullable=true),
 *   @OA\Property(property="tracking_code", type="string", format="uuid"),
 *   @OA\Property(property="status", type="string", enum={"created","in_transit","delivered","cancelled"}),
 *   @OA\Property(property="origin_city", type="string"),
 *   @OA\Property(property="origin_address", type="string"),
 *   @OA\Property(property="destination_city", type="string"),
 *   @OA\Property(property="destination_address", type="string"),
 *   @OA\Property(property="weight_grams", type="integer"),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */


class Package extends Model
{
    use SoftDeletes, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sender_id',
        'carrier_id',
        'tracking_code',
        'status',
        'origin_city',
        'origin_address',
        'destination_city',
        'destination_address',
        'weight_grams'
    ];
}
