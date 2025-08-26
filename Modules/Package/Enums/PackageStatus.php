<?php

namespace Modules\Package\Enums;

use App\Traits\Enumable;

enum PackageStatus: string
{
    use Enumable;

    case CREATED    = 'created';
    case IN_TRANSIT = 'in_transit';
    case DELIVERED  = 'delivered';
    case CANCELLED  = 'cancelled';

    public static function statuses()
    {
        return [
            'created'    => self::created(),
            'in_transit' => self::inTransit(),
            'delivered'  => self::delivered(),
            'cancelled'  => self::cancelled(),
        ];
    }

    public static function created()
    {
        return [
            'translate' => __('package::package.created'),
            'icon'      => 'bi bi-cart-check-fill',
            'color'     => 'badge badge-light-info',
            'route'     => 'packages.create'
        ];
    }

    public static function inTransit()
    {
        return [
            'translate' => __('package::package.in_transit'),
            'icon'      => 'bi bi-truck',
            'color'     => 'badge badge-light-warning',
            'route'     => 'packages.transit'
        ];
    }

    public static function delivered()
    {
        return [
            'translate' => __('package::package.delivered'),
            'icon'      => 'bi bi-box-seam-fill',
            'color'     => 'badge badge-light-success',
            'route'     => 'packages.deliver'
        ];
    }

    public static function cancelled()
    {
        return [
            'translate' => __('package::package.cancelled'),
            'icon'      => 'bi bi-x-circle-fill',
            'color'     => 'badge badge-light-danger',
            'route'     => 'packages.cancel'
        ];
    }
}
