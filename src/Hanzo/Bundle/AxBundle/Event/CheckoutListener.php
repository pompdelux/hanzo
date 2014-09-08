<?php

namespace Hanzo\Bundle\AxBundle\Event;

use Hanzo\Bundle\AxBundle\Actions\Out\OrderAlreadyInQueueException;
use Hanzo\Bundle\AxBundle\Actions\Out\PheanstalkQueue;
use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;
use Hanzo\Core\ServiceLogger;
use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersStateLog;

class CheckoutListener
{
    /**
     * @var PheanstalkQueue
     */
    protected $pheanstalkQueue;

    /**
     * @var ServiceLogger
     */
    protected $logger;


    /**
     * @param PheanstalkQueue $pheanstalkQueue
     * @param ServiceLogger   $logger
     */
    public function __construct(PheanstalkQueue $pheanstalkQueue, ServiceLogger $logger)
    {
        $this->pheanstalkQueue = $pheanstalkQueue;
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
        $order = $event->getOrder();

        try {
            $id = $this->pheanstalkQueue->appendSendOrder($order, $order->getInEdit());
            $this->stateLog($order->getId());
            $message = 'Order added to beanstalk queue (initial add).';
        } catch (OrderAlreadyInQueueException $e) {
            $message = $e->getMessage();
        }

        $this->logger->debug($message, [
            'job_id'   => $id,
            'order_id' => $order->getId(),
            'queue'    => 'orders2ax',
        ]);
    }

    private function stateLog($order_id)
    {
        $log = new OrdersStateLog();
        $log->setOrdersId($order_id);
        $log->setState(0);
        $log->setMessage(Orders::INFO_STATE_IN_QUEUE);
        $log->setCreatedAt(time());

        return $log->save();
    }
}
