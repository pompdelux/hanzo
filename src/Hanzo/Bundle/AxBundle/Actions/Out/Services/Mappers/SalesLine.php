<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AxBundle\Actions\Out\Services\Mappers;

/**
 * Class SalesLine
 * @package Hanzo\Bundle\AxBundle
 */
class SalesLine extends BaseMapper
{
    protected $fields = [
        'ItemId'          => null,
        'SalesPrice'      => null,
        'SalesQty'        => null,
        'SalesUnit'       => null,
        'InventColorId'   => null,
        'InventSizeId'    => null,
        'LineDiscAmt'     => null,
        'LineDiscPercent' => null,
        'SalesLineText'   => null,
        'VoucherCode'     => null,
    ];
}
