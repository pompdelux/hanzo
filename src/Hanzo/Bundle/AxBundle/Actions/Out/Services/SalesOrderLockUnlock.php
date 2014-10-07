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
 * Class SalesOrderLockUnlock
 * 
 * @package Hanzo\Bundle\AxBundle
 */
class SalesOrderLockUnlock extends BaseService
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->data = (object) [
            'eOrderNumber'   => null,
            'lockOrder'      => 1,
            'endpointDomain' => ''
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $this->data->endpointDomain = $this->getEndPoint();

        return $this->data;
    }

    /**
     * Set data used in lock/unlock action
     *
     * @param Orders $order
     * @param bool   $lock
     */
    public function setData(Orders $order, $lock = true)
    {
        $this->data->eOrderNumber = $order->getId();
        $this->data->lockOrder    = (int) $lock;
    }
}
