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
 * Class CustTable
 *
 * @package Hanzo\Bundle\AxBundle\Actions\Out\Services\Mappers
 */
class CustTable extends BaseMapper
{
    protected $fields = [
        'AccountNum'             => null,
        'AddressCity'            => null,
        'AddressCountryRegionId' => null,
        'AddressStreet'          => null,
        'AddressZipCode'         => null,
        'CustName'               => null,
        'Email'                  => null,
        'InitialsId'             => null,
        'Phone'                  => null,
    ];
}
