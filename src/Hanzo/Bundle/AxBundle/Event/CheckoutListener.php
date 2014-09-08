<?php

namespace Hanzo\Bundle\AxBundle\Event;

use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;
use Hanzo\Core\ServiceLogger;
use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersStateLog;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxy;

class CheckoutListener
{
    /**
     * @var PheanstalkProxy
     */
    protected $pheanstalk;

    /**
     * @var ServiceLogger
     */
    protected $logger;


    /**
     * @param PheanstalkProxy $pheanstalk
     * @param ServiceLogger   $logger
     */
    public function __construct(PheanstalkProxy $pheanstalk, ServiceLogger $logger)
    {
        $this->pheanstalk = $pheanstalk;
        $this->logger     = $logger;
    }


    /**
     * Sync order to AX
     *
     * If this fails completely, propagation is halted.
     *
     * @param  FilterOrderEvent $event
     */
    public function onPaymentCollected(FilterOrderEvent $event)
    {
        $order    = $event->getOrder();
        $endPoint = Tools::domainKeyToEndpoint($order->getAttributes()->global->domain_key);

        $id = $this->pheanstalk->putInTube('orders2ax', json_encode([
            'order_id'      => $order->getId(),
            'order_in_edit' => $event->getInEdit(),
            'customer_id'   => $order->getCustomersId(),
            'iteration'     => 0,
            'end_point'     => $endPoint,
            'db_conn'       => 'pdldb'.strtolower($endPoint).'1',
        ]));

        $log = new OrdersStateLog();
        $log->setOrdersId($order->getId());
        $log->setState(0);
        $log->setMessage(Orders::INFO_STATE_IN_QUEUE);
        $log->setCreatedAt(time());
        $log->save();

        $this->logger->debug('Order added to beanstalk queue (initial add).', [
            'job_id'   => $id,
            'order_id' => $order->getId(),
            'queue'    => 'orders2ax',
        ]);
    }
}
