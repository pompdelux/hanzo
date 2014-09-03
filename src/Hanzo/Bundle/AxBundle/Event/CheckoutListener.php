<?php

namespace Hanzo\Bundle\AxBundle\Event;

use Propel;

use Hanzo\Model\OrdersSyncLogQuery;
use Hanzo\Bundle\AxBundle\Actions\Out\AxService;
use Hanzo\Bundle\CheckoutBundle\Event\FilterOrderEvent;
use Symfony\Bridge\Monolog\Logger;

class CheckoutListener
{
    protected $logger;
    protected $ax;

    public function __construct(AxService $ax, Logger $logger)
    {
        $this->ax = $ax;
        $this->logger = $logger;
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
        $order  = $event->getOrder();
        $logged = false;

        if ($this->ax->sendOrder($order, false, null, $event->getInEdit())) {
            $logged = OrdersSyncLogQuery::create()
                ->select('State')
                ->filterByOrdersId($order->getId())
                ->findOne(Propel::getConnection(null, Propel::CONNECTION_WRITE))
            ;
        }

        if (!$logged) {
            $this->logger->addError('Order #'.$order->getId().' was not send to AX and error not written to db!');
            $event->stopPropagation();
        }
    }
}
