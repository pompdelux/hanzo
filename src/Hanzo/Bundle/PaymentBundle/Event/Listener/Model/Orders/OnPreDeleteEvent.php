<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\PaymentBundle\Event\Listener\Model\Orders;

use Glorpen\Propel\PropelBundle\Events\ModelEvent;
use Hanzo\Bundle\PaymentBundle\PaymentActionsProxy;
use Hanzo\Model\Orders;

/**
 * Class OnPreDeleteEvent
 * @package Hanzo\Bundle\PaymentBundle
 */
class OnPreDeleteEvent
{
    /**
     * @var PaymentActionsProxy
     */
    private $actionsProxy;

    /**
     * @param PaymentActionsProxy $actionsProxy
     */
    public function __construct(PaymentActionsProxy $actionsProxy)
    {
        $this->actionsProxy = $actionsProxy;
    }

    /**
     * @param  ModelEvent $event
     * @throws \Exception
     */
    public function handle(ModelEvent $event)
    {
        $order = $event->getModel();

        if ((!$order instanceof Orders) ||
            ($order->getState() < Orders::STATE_PAYMENT_OK)
        ) {
            return;
        }

        try {
            $this->actionsProxy->cancelPayment($order);
        } catch (\Exception $e) {
            if (false === $order->getIgnoreDeleteConstraints()) {
                throw $e;
            }
        }
    }
}
