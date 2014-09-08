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
 * Class SalesTableDelete
 * @package Hanzo\Bundle\AxBundle
 */
class SalesTableDelete extends BaseMapper
{
    protected $fields = [
        'CustAccount'     => null,
        'EOrderNumber'    => null,
        'PaymentId'       => null,
        'SalesType'       => 'Sales',
        'Completed'       => 1,
        'TransactionType' => 'Delete',
    ];
}
