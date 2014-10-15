<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\BasketBundle\Event\Listener\Model\Orders;

use Glorpen\Propel\PropelBundle\Events\ModelEvent;
use Hanzo\Model\Orders;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class OnPostSaveEvent
 * @package Hanzo\Bundle\BasketBundle
 */
class OnPostSaveEvent
{
    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param ModelEvent $event
     */
    public function handle(ModelEvent $event)
    {
        $order = $event->getModel();

        if ((!$order instanceof Orders) || ('cli' == PHP_SAPI)) {
            return;
        }

        if (false === $this->session->has('order_id')) {
            $this->session->set('order_id', $order->getId());
        }
    }
}
