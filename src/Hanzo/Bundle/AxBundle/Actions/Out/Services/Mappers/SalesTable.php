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
 * Class SalesTable
 * @package Hanzo\Bundle\AxBundle
 */
class SalesTable extends BaseMapper
{
    protected $fields = [
        'CustAccount' => null,
        'EOrderNumber' => null,
        'PaymentId' => null,
        'HomePartyId' => null,
        'SalesResponsible' => null,
        'CurrencyCode' => null,
        'SalesName' => null,
        'SalesGroup' => null,
        'SalesType' => 'Sales',
        'SalesLine' => [],
        'InvoiceAccount' => null,
        'FreightFeeAmt' => null,
        'FreightType' => null,
        'HandlingFeeType' => 90,
        'HandlingFeeAmt' => null,
        'PayByBillFeeType' => 91,
        'PayByBillFeeAmt' => null,
        'Completed' => 1,
        'TransactionType' => 'Write',
        'CustPaymMode' => null,
        'SmoreContactInfo' => null,
        'BankAccountNumber' => null,
        'BankId' => null,
        'DeliveryDropPointId' => null,
        'DeliveryCompanyName' => null,
        'DeliveryCity' => null,
        'DeliveryName' => null,
        'DeliveryStreet' => null,
        'DeliveryZipCode' => null,
        'DeliveryCountryRegionId' => null,
    ];
}
