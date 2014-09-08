<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AxBundle\Actions\Out\Services;

use Hanzo\Bundle\AxBundle\Actions\Out\Services\Mappers\SalesTableDelete;
use Hanzo\Model\Orders;

/**
 * Class SyncDeleteSalesOrder
 * @package Hanzo\Bundle\AxBundle
 */
class SyncDeleteSalesOrder extends BaseService
{
    /**
     * Construct
     */
    public function __construct()
    {
        $this->data = (object) [
            'endpointDomain' => '',
            'salesOrder' => (object) [
                'SalesTable' => null
            ]
        ];
    }

    /**
     * Set data needed to complete request
     *
     * @param Orders $order
     * @param string $payment_id
     */
    public function setData(Orders $order, $payment_id = '')
    {
        $this->data->salesOrder->SalesTable = new SalesTableDelete([
            'CustAccount'  => $order->getCustomersId(),
            'EOrderNumber' => $order->getId(),
            'PaymentId'    => $payment_id,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function send($name = null)
    {
        return parent::send('SyncSalesOrder');
    }
}
