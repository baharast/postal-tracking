<?php

namespace Modules\Package\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
