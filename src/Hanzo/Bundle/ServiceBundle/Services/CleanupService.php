<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Criteria;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Bundle\PaymentBundle\Dibs\DibsApi;

use Hanzo\Model\Orders;
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
     * @return int number of deleted orders
     */
    public function deleteDeadOrders()
    {
        // first we nuke orders, wich:
        // - have not been touched in the last 2 hours
        //- and have a negative status
        $orders = OrdersQuery::create()
            ->filterByState(0, Criteria::LESS_THAN)
            ->filterByUpdatedAt(date('Y-m-d H:i:s', strtotime('2 hours ago')), Criteria::LESS_THAN)
            ->find()
        ;

        if ($count = $orders->count()) {
            $orders->delete();
        }

        return $count;
    }


    /**
     * try to handle orders with failed callbacks
     *
     * @return [type] [description]
     */
    public function failedPaymentOrders()
    {
      return true;

        $orders = OrdersQuery::create()
            ->filterByState(0, Criteria::GREATER_THAN)
            ->filterByState(Orders::STATE_SHIPPED, Criteria::LESS_THAN)
            ->filterByPaymentGatewayId(NULL, Criteria::ISNULL)
            ->filterByBillingMethod('dibs')
            ->filterByInEdit(0)
            ->filterByUpdatedAt(date('Y-m-d H:i:s', strtotime('2 hours ago')), Criteria::LESS_THAN)
            ->find()
        ;

        foreach ($orders as $order) {
            $transaction = OrdersAttributesQuery::create()
                ->filterByOrdersId($order->getId())
                ->filterByCKey('transact')
                ->filterByNS('payment:gateway')
                ->findOne()
            ;

            if ($transaction) {
                $transaction = $transaction->getCValue();

            }
        }
    }
}
