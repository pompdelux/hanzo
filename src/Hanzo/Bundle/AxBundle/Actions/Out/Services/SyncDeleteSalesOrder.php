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
        $this->data = [
            'endpointDomain' => '',
            'salesOrder' => [
                'SalesTable' => null
            ]
        ];
    }

    /**
     * Set data needed to complete request
     *
     * @param Orders $order
     */
    public function setOrder(Orders $order)
    {
        $this->data['salesOrder']['SalesTable'] = [
            'CustAccount'  => $order->getCustomersId(),
            'EOrderNumber' => $order->getId(),
            'PaymentId'    => $order->getPaymentTransactionId(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $this->data['endpointDomain'] = $this->getEndPoint();

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
