<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\BasketBundle\Service;

use Hanzo\Bundle\AxBundle\Actions\Out\AxServiceWrapper;
use Hanzo\Bundle\PaymentBundle\PaymentActionsProxy;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\OrdersStateLog;

/**
 * Class Cleanup
 *
 * @package Hanzo\Bundle\BasketBundle\Service
 */
class Cleanup
{
    private $dryRun;
    private $trigger;
    private $paymentActionsProxy;
    private $axServiceWrapper;

    /**
     * constructor
     *
     * @param PaymentActionsProxy $paymentActionsProxy
     * @param AxServiceWrapper    $axServiceWrapper
     */
    public function __construct(PaymentActionsProxy $paymentActionsProxy, AxServiceWrapper $axServiceWrapper)
    {
        $this->paymentActionsProxy = $paymentActionsProxy;
        $this->axServiceWrapper    = $axServiceWrapper;
    }

    /**
     * @param bool $state
     */
    public function setDryRun($state = false)
    {
        $this->dryRun = (bool) $state;
    }

    /**
     * @param string $name
     */
    public function setTrigger($name)
    {
        $this->trigger = $name;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function run()
    {
        if (empty($this->trigger)) {
            throw new \InvalidArgumentException('You must set a trigger - this is needed to document where deletes come from.');
        }

        // 1. cancel order edits with no activity
        /** @var Orders $order */
        foreach ($this->findStaleEditOrders() as $order) {
            $api = strtolower($order->getBillingMethod().'api');

            if ('api' === $api) {
                $this->handleUnknownBillingMethod($order);
                continue;
            }

            $api = $this->paymentActionsProxy->getApiByName($api);
            if (! $api) {
                $this->handleUnknownBillingMethod($order);
                continue;
            }

            if (!method_exists($api, 'handleStaleOrderEdit')) {
                $this->handleUnknownBillingMethod($order);
                continue;
            }

            $api->handleStaleOrderEdit($order);
        }

        // 2. delete (or resolve) dead orders

    }


    /**
     * @return array|mixed|\PropelObjectCollection
     */
    protected function findStaleEditOrders()
    {
        return OrdersQuery::create()
            ->filterByInEdit(true)
            ->filterByState(Orders::STATE_PENDING, \Criteria::LESS_THAN)
            ->filterByState(Orders::STATE_ERROR_PAYMENT, \Criteria::GREATER_THAN)
            ->filterByUpdatedAt(date('Y-m-d H:i:s', strtotime('2 hours ago')), \Criteria::LESS_THAN)
            ->filterByCreatedAt(date('Y-m-d H:i:s', strtotime('6 month ago')), \Criteria::GREATER_THAN)
            ->find();

    }


    /**
     * @param Orders $order
     *
     * @return mixed
     * @throws \Exception
     * @throws \PropelException
     */
    protected function handleUnknownBillingMethod(Orders $order)
    {
        if ($this->dryRun) {
            return error_log('['.date('Y-m-d H:i:s').'] Order: #'.$order->getId().' will be roled back one version and unlocked in AX.');
        }

        $order->toPreviousVersion();
        $this->axServiceWrapper->SalesOrderLockUnlock($order, false);

        $log = new OrdersStateLog();
        $log->info($order->getId(), Orders::INFO_STATE_EDIT_CANCLED_BY_CLEANUP);
        $log->save();
    }
}
