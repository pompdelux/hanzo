<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Criteria;
use Hanzo\Model\OrdersStateLog;
use Propel;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersQuery;

class CleanupService
{
    protected $parameters;
    protected $settings;

    /**
     * __construct
     *
     * @param array $parameters
     * @param array $settings
     */
    public function __construct($parameters, $settings)
    {
        $this->parameters = $parameters;
        $this->settings = $settings;
    }


    /**
     * delete orders deemed dead
     *
     * @param boolean $dry_run testing flag.
     * @return int
     */
    public function deleteStaleOrders($dry_run = false)
    {
        Propel::setForceMasterConnection(true);
        $orders = OrdersQuery::create()
            ->filterByBillingMethod(['dibs', 'pensio'], Criteria::NOT_IN)
            ->_or()
            ->filterByBillingMethod(null, Criteria::ISNULL)
            ->filterByState(0, Criteria::LESS_THAN)
            ->filterByState(Orders::STATE_ERROR_PAYMENT, Criteria::GREATER_THAN)
            ->filterByUpdatedAt(date('Y-m-d H:i:s', strtotime('2 hours ago')), Criteria::LESS_THAN)
            ->filterByCreatedAt(date('Y-m-d H:i:s', strtotime('6 month ago')), Criteria::GREATER_THAN)
            ->filterByInEdit(false)
            ->find()
        ;

        $count = 0;
        foreach ($orders as $order) {
            $attributes = $order->getAttributes();

            // only delete orders which has no payment info attached
            if (empty($attributes->payment) || empty($attributes->payment->transact)) {
                $count++;
                if ($dry_run) {
                    error_log('['.date('Y-m-d H:i:s').'] Order: #'.$order->getId().' will be deleted, state is: '.$order->getState());
                    continue;
                }

                $order->setIgnoreDeleteConstraints(true);
                $order->delete();
            }
        }

        return $count;

        Propel::setForceMasterConnection(false);
    }


    /**
     * cancel order edits, where people abandon their edit session without releasing the edit lock
     *
     * @param  Container $container service container
     * @param  boolean   $dry_run   testing flag.
     * @return int
     */
    public function cancelStaleOrderEdit($container, $dry_run = false)
    {
        Propel::setForceMasterConnection(true);

        // extended to include records where billing_method is null
        $orders = OrdersQuery::create()
            ->filterByInEdit(true)
            ->filterByBillingMethod(['dibs', 'pensio'], Criteria::NOT_IN)
            ->_or()
            ->filterByBillingMethod(null, Criteria::ISNULL)
            ->filterByState(Orders::STATE_PENDING, Criteria::LESS_THAN)
            ->filterByState(Orders::STATE_ERROR_PAYMENT, Criteria::GREATER_THAN)
            ->filterByUpdatedAt(date('Y-m-d H:i:s', strtotime('2 hours ago')), Criteria::LESS_THAN)
            ->find()
        ;

        $count = 0;
        foreach ($orders as $order) {
            $count++;
            if ($dry_run) {
                error_log('['.date('Y-m-d H:i:s').'] Order: #'.$order->getId().' will be roled back one version and unlocked in AX.');
                continue;
            }

            $order->toPreviousVersion();
            $container->get('ax.out.service.wrapper')->SalesOrderLockUnlock($order, false);

            $log = new OrdersStateLog();
            $log->setOrdersId($order->getId());
            $log->setState(0);
            $log->setMessage(Orders::INFO_STATE_EDIT_CANCLED_BY_CLEANUP);
            $log->setCreatedAt(time());
            $log->save();
        }

        return $count;

        Propel::setForceMasterConnection(false);
    }
}
