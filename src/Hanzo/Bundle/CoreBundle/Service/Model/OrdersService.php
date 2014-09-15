<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\CoreBundle\Service\Model;

use Hanzo\Bundle\AxBundle\Actions\Out\PheanstalkQueue;
use Hanzo\Bundle\AxBundle\Logger;
use Hanzo\Bundle\CoreBundle\Core;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersSyncLogQuery;

class OrdersService
{
    /**
     * @var Core
     */
    private $hanzoCore;

    /**
     * @var PheanstalkQueue
     */
    private $pheanstalkQueue;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Core                $core
     * @param PheanstalkQueue     $pheanstalkQueue
     * @param Logger $logger
     */
    public function __construct(Core $core, PheanstalkQueue $pheanstalkQueue, Logger $logger)
    {
        $this->hanzoCore           = $core;
        $this->pheanstalkQueue     = $pheanstalkQueue;
        $this->logger              = $logger;
    }

    /**
     * This triggers a series of events:
     *   1. send delete command to ax (but only if it's already send to ax)
     *   2. send delete command to payment provider
     *   3. backup full order
     *   4. delete order from db
     *
     * @param  Orders $order
     * @return int
     * @throws \Hanzo\Bundle\AxBundle\Actions\Out\OrderAlreadyInQueueException
     */
    public function deleteOrder(Orders $order)
    {
        $syncCheck = OrdersSyncLogQuery::create()
            ->filterByOrdersId($order->getId())
            ->filterByState('ok')
            ->count($order->getDBConnection());

        if ($syncCheck) {
            $this->pheanstalkQueue->appendDeleteOrder($order);
        }

        try {
            $order->delete($order->getDBConnection());
        } catch (\Exception $e) {
            $this->logger->write($order->getId(), 'failed', [], $e->getMessage());
        }
    }
}
