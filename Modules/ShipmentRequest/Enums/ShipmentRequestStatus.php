<?php

namespace Modules\ShipmentRequest\Enums;

use App\Traits\Enumable;

enum ShipmentRequestStatus: string
{
    use Enumable;

    case PENDING  = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';

    public static function statuses()
    {
        return [
            'pending'  => self::pending(),
            'accepted' => self::accepted(),
            'rejected' => self::rejected(),
        ];
    }

    public static function pending()
    {
        return [
            'translate' => __('shipment_request::shipment_request.pending'),
            'icon'      => 'bi bi-hourglass-split',
            'color'     => 'badge badge-light-info',
            'route'     => 'packages.pending'
        ];
    }

    public static function accepted()
    {
        return [
            'translate' => __('shipment_request::shipment_request.accepted'),
            'icon'      => 'bi bi-check-circle-fill',
            'color'     => 'badge badge-light-success',
            'route'     => 'packages.accept'
        ];
    }

    public static function rejected()
    {
        return [
            'translate' => __('shipment_request::shipment_request.rejected'),
            'icon'      => 'bi bi-x-circle-fill',
            'color'     => 'badge badge-light-danger',
            'route'     => 'packages.reject'
        ];
    }
}
