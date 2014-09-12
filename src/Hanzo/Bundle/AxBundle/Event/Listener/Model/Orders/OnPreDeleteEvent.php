<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AxBundle\Event\Listener\Model\Orders;

use Glorpen\Propel\PropelBundle\Events\ModelEvent;
use Hanzo\Bundle\AxBundle\Actions\Out\PheanstalkQueue;
use Hanzo\Model\Orders;

/**
 * Class OnPreDeleteEvent
 * @package Hanzo\Bundle\AxBundle
 */
class OnPreDeleteEvent
{
    /**
     * @var PheanstalkQueue
     */
    private $pheanstalkQueue;

    /**
     * @param PheanstalkQueue $pheanstalkQueue
     */
    public function __construct(PheanstalkQueue $pheanstalkQueue)
    {
        $this->pheanstalkQueue = $pheanstalkQueue;
    }

    /**
     * @param ModelEvent $event
     */
    public function handle(ModelEvent $event)
    {
        $order = $event->getModel();

        if (!$order instanceof Orders) {
            return;
        }

        $this->pheanstalkQueue->appendDeleteOrder($order);
    }
}
