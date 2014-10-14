<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AxBundle\Actions\Out;

use Hanzo\Bundle\AxBundle\Actions\Out\Services\SalesOrderLockUnlock;
use Hanzo\Bundle\AxBundle\Actions\Out\Services\SyncCustomer;
use Hanzo\Bundle\AxBundle\Actions\Out\Services\SyncDeleteSalesOrder;
use Hanzo\Bundle\AxBundle\Actions\Out\Services\SyncSalesOrder;
use Hanzo\Core\Tools;
use Hanzo\Model\AddressesQuery;
use Hanzo\Model\Customers;
use Hanzo\Model\Orders;

/**
 * Class AxServiceWrapper
 *
 * @package Hanzo\Bundle\AxBundle\Actions\Out
 */
class AxServiceWrapper
{
    /**
     * @var Services\SyncCustomer
     */
    private $syncCustomer;

    /**
     * @var Services\SyncSalesOrder
     */
    private $syncSalesOrder;

    /**
     * @var Services\SyncDeleteSalesOrder
     */
    private $syncDeleteSalesOrder;

    /**
     * @var Services\SalesOrderLockUnlock
     */
    private $salesOrderLockUnlock;

    /**
     * @var mixed
     */
    private $errors;

    /**
     * Construct
     *
     * @param SyncCustomer         $syncCustomer
     * @param SyncSalesOrder       $syncSalesOrder
     * @param SyncDeleteSalesOrder $syncDeleteSalesOrder
     * @param SalesOrderLockUnlock $salesOrderLockUnlock
     */
    public function __construct(SyncCustomer $syncCustomer, SyncSalesOrder $syncSalesOrder, SyncDeleteSalesOrder $syncDeleteSalesOrder, SalesOrderLockUnlock $salesOrderLockUnlock)
    {
        $this->syncCustomer         = $syncCustomer;
        $this->syncSalesOrder       = $syncSalesOrder;
        $this->syncDeleteSalesOrder = $syncDeleteSalesOrder;
        $this->salesOrderLockUnlock = $salesOrderLockUnlock;
        $this->errors               = null;
    }

    /**
     * Wraps SyncCustomer service and handles setters n' stuff.
     *
     * @param Customers $customer
     * @param bool      $return
     * @param null      $dbCon
     *
     * @return \stdClass|bool
     */
    public function SyncCustomer(Customers $customer, $return = false, $dbCon = null)
    {
        $address = AddressesQuery::create()
            ->joinWithCountries()
            ->filterByType('payment')
            ->filterByCustomersId($customer->getId())
            ->findOne($dbCon);

        $this->syncCustomer->reset();
        $this->syncCustomer->setEndPoint(Tools::domainKeyToEndpoint($address->getCountries()->getIso2()));
        $this->syncCustomer->setCustomer($customer);
        $this->syncCustomer->setAddress($address);
        $this->syncCustomer->setDBConnection($dbCon);

        if ($return) {
            $result       = $this->syncCustomer->get();
            $this->errors = $this->syncCustomer->getErrors();

            return $result;
        }

        $result       = $this->syncCustomer->send();
        $this->errors = $this->syncCustomer->getErrors();

        return $result;
    }

    /**
     * Wraps SyncSalesOrder service and handles setters n' stuff.
     *
     * @param Orders $order
     * @param bool   $return
     * @param null   $dbCon
     * @param bool   $inEdit
     *
     * @return \stdClass|bool
     */
    public function SyncSalesOrder(Orders $order, $return = false, $dbCon = null, $inEdit = false)
    {
        $endPoint = $order->getEndPoint();
        if (!$endPoint) {
            $endPoint = Tools::domainKeyToEndpoint($order->getAttributes($dbCon)->global->domain_key);
        }

        $this->syncSalesOrder->reset();
        $this->syncSalesOrder->setDBConnection($dbCon);
        $this->syncSalesOrder->setEndPoint($endPoint);
        $this->syncSalesOrder->setInEdit($inEdit);
        $this->syncSalesOrder->setOrder($order);
        $this->syncSalesOrder->setOrderLines($order->getOrdersLiness(null, $dbCon));
        $this->syncSalesOrder->setOrderAttributes($order->getOrdersAttributess(null, $dbCon));

        if ($return) {
            $result       = $this->syncSalesOrder->get();
            $this->errors = $this->syncSalesOrder->getErrors();

            return $result;
        }

        $result       = $this->syncSalesOrder->send();
        $this->errors = $this->syncSalesOrder->getErrors();

        return $result;
    }

    /**
     * Wraps SyncDeleteSalesOrder service and handles setters n' stuff.
     *
     * @param Orders $order
     * @param null   $dbCon
     * @param bool   $return
     *
     * @return \stdClass|bool
     */
    public function SyncDeleteSalesOrder(Orders $order, $dbCon = null, $return = false)
    {
        $this->syncDeleteSalesOrder->reset();
        $this->syncDeleteSalesOrder->setDBConnection($dbCon);
        $this->syncDeleteSalesOrder->setEndPoint($order->getEndPoint());
        $this->syncDeleteSalesOrder->setOrder($order);

        if ($return) {
            $result       = $this->syncDeleteSalesOrder->get();
            $this->errors = $this->syncDeleteSalesOrder->getErrors();

            return $result;
        }

        $result       = $this->syncDeleteSalesOrder->send();
        $this->errors = $this->syncDeleteSalesOrder->getErrors();

        return $result;
    }

    /**
     * Wraps SalesOrderLockUnlock service and handles setters n' stuff.
     *
     * @param Orders $order
     * @param bool   $lock
     * @param null   $dbCon
     * @param bool   $return
     *
     * @return \stdClass|bool
     */
    public function SalesOrderLockUnlock(Orders $order, $lock = true, $dbCon = null, $return = false)
    {
        $this->salesOrderLockUnlock->reset();
        $this->salesOrderLockUnlock->setData($order, $lock);
        $this->salesOrderLockUnlock->setDBConnection($dbCon);
        $this->salesOrderLockUnlock->setEndPoint(Tools::domainKeyToEndpoint($order->getAttributes($dbCon)->global->domain_key));

        if ($return) {
            $result       = $this->salesOrderLockUnlock->get();
            $this->errors = $this->salesOrderLockUnlock->getErrors();

            return $result;
        }

        $result       = $this->salesOrderLockUnlock->send();
        $this->errors = $this->salesOrderLockUnlock->getErrors();

        return $result;
    }

    /**
     * Get any errors
     *
     * @return mixed|null
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
