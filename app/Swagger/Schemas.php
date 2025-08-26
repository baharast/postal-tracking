<?php

use OpenApi\Annotations as OA;

/**
 * ---------- Shared / Common Schemas ----------
 */

/**
 * @OA\Schema(
 *   schema="PaginationMeta",
 *   type="object",
 *   @OA\Property(
 *     property="pagination", type="object",
 *     @OA\Property(property="current_page", type="integer", example=1),
 *     @OA\Property(property="per_page", type="integer", example=15),
 *     @OA\Property(property="total", type="integer", example=42)
 *   )
 * )
 */

/**
 * {
 *   "data": {...} | null,
 *   "meta": {...} | null,
 *   "errors": [{code,detail,field?}] | null
 * }
 */
 /**
  * @OA\Schema(
  *   schema="ApiError",
  *   type="object",
  *   required={"code","detail"},
  *   @OA\Property(property="code", type="string", example="validation_error"),
  *   @OA\Property(property="detail", type="string", example="The given data was invalid."),
  *   @OA\Property(property="field", type="string", nullable=true, example="email")
  * )
  */

 /**
  * @OA\Schema(
  *   schema="ApiEnvelope_Object",
  *   type="object",
  *   @OA\Property(property="data", type="object", nullable=true),
  *   @OA\Property(property="meta", type="object", nullable=true),
  *   @OA\Property(property="errors", type="array", nullable=true, @OA\Items(ref="#/components/schemas/ApiError"))
  * )
  */

 /**
  * @OA\Schema(
  *   schema="ApiEnvelope_List",
  *   type="object",
  *   @OA\Property(property="data", type="array", @OA\Items(type="object")),
  *   @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta"),
  *   @OA\Property(property="errors", type="array", nullable=true, @OA\Items(ref="#/components/schemas/ApiError"))
  * )
  */

 /**
  * ---------- Domain Request Schemas ----------
  */
 /**
  * @OA\Schema(
  *   schema="PackageCreateRequest",
  *   type="object",
  *   required={"origin_city","origin_address","destination_city","destination_address","weight_grams"},
  *   @OA\Property(property="origin_city", type="string"),
  *   @OA\Property(property="origin_address", type="string"),
  *   @OA\Property(property="destination_city", type="string"),
  *   @OA\Property(property="destination_address", type="string"),
  *   @OA\Property(property="weight_grams", type="integer", minimum=0, example=1200)
  * )
  */
