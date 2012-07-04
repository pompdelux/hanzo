<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Criteria;
use Propel;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Bundle\PaymentBundle\Dibs\DibsApi;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersQuery;

class CleanupService
{
    protected $parameters;
    protected $settings;

    protected $dibs;

    /**
     * __construct
     *
     * @param array $parameters
     * @param array $settings
     */
    public function __construct($parameters, $settings)
    {
        $this->dibs = $parameters[0];
        $this->parameters = $parameters;
        $this->settings = $settings;

        if (!$this->dibs instanceof DibsApi) {
             throw new \InvalidArgumentException('DibsApi expected as first parameter.');
        }
    }


    /**
     * delete orders deemed dead
     *
     * @param boolean $dry_run testing flag.
     */
    public function deleteStaleOrders($dry_run = false)
    {
        Propel::setForceMasterConnection(true);

        $orders = OrdersQuery::create()
            ->joinWithOrdersAttributes()
            ->filterByState(0, Criteria::LESS_THAN)
            ->filterByState(Orders::STATE_ERROR_PAYMENT, Criteria::GREATER_THAN)
            ->filterByUpdatedAt(date('Y-m-d H:i:s', strtotime('2 hours ago')), Criteria::LESS_THAN)
            ->filterByInEdit(false)
            ->find()
        ;

        foreach ($orders as $order) {
            if (isset($order->getAttributes()->payment) && empty($order->getAttributes()->payment->transact)) {
                if ($dry_run) {
                    error_log('['.date('Y-m-d H:i:s').'] Order: #'.$order->getId().' will be deleted, state is: '.$order->getState());
                    continue;
                }

                $order->delete();
            }
        }

        Propel::setForceMasterConnection(false);
    }


    /**
     * cancel order edits, where people abandon their edit session without releasing the edit lock
     *
     * @param  Container $container service container
     * @param  boolean   $dry_run   testing flag.
     */
    public function cancelStaleOrderEdit($container, $dry_run = false)
    {
        Propel::setForceMasterConnection(true);

        $orders = OrdersQuery::create()
            ->filterByInEdit(true)
            ->filterByUpdatedAt(array('max' => '-3 hours'))
            ->filterByState(Orders::STATE_PENDING, Criteria::LESS_THAN)
            ->filterByState(Orders::STATE_ERROR_PAYMENT, Criteria::GREATER_THAN)
            ->find()
        ;

        foreach ($orders as $order) {
            if ($dry_run) {
                error_log('['.date('Y-m-d H:i:s').'] Order: #'.$order->getId().' will be roled back one version and unlocked in AX.');
                continue;
            }

            $order->toPreviousVersion();
            $container->get('ax_manager')->lockUnlockSalesOrder($order, false);
        }

        // // should _not_ be necessary, but...
        // $orders = OrdersQuery::create()
        //     ->filterByInEdit(true)
        //     ->filterByState(array(
        //         Orders::STATE_BEING_PROCESSED,
        //         Orders::STATE_SHIPPED,
        //     ))
        //     ->find()
        // ;

        // foreach ($orders as $order) {
        //     $order->setInEdit(false);
        //     $order->save();
        // }

        Propel::setForceMasterConnection(false);
    }
}
