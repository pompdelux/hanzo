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
use Hanzo\Model\CustomersPeer;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersDeletedLog;
use Hanzo\Model\OrdersDeletedLogQuery;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OnPreDeleteEvent
 * @package Hanzo\Bundle\BasketBundle
 */
class OnPreDeleteEvent
{
    /**
     * @var ContainerInterface
     */
    private $serviceContainer;

    /**
     * @param ContainerInterface $serviceContainer
     */
    public function __construct(ContainerInterface $serviceContainer = null)
    {
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * @param ModelEvent $event
     */
    public function handle(ModelEvent $event)
    {
        $order = $event->getModel();

        if ((!$order instanceof Orders)) {
            return;
        }

        // If the order is:
        // - empty (new)
        // - customers_id and email is empty
        // we skip saving backup.
        if (($order->isNew()) ||
            (!$order->getCustomersId() && !$order->getEmail())
        ) {
            return;
        }

        $conn = $order->getDBConnection();
        $data = [];

        $data['orders']            = $order->toArray();
        $data['orders_attributes'] = $order->getOrdersAttributess(null, $conn)->toArray();
        $data['orders_lines']      = $order->getOrdersLiness(null, $conn)->toArray();
        $data['orders_state_log']  = $order->getOrdersStateLogs(null, $conn)->toArray();
        $data['orders_versions']   = $order->getOrdersVersionss(null, $conn)->toArray();

        if (defined('ACTION_TRIGGER')) {
            $trigger    = 'cli';
            $deleted_by = ACTION_TRIGGER;
        } else {
            $trigger    = $this->serviceContainer->get('request')->getUri();
            $deleted_by = 'cid: ' . CustomersPeer::getCurrent()->getId();
        }

        $entry = OrdersDeletedLogQuery::create()->findOneByOrdersId($order->getId());

        if (!$entry instanceof OrdersDeletedLog) {
            $entry = new OrdersDeletedLog();
            $entry->setOrdersId($order->getId());
            $entry->setCustomersId($order->getCustomersId());
            $entry->setName($order->getFirstName().' '.$order->getLastName());
            $entry->setEmail($order->getEmail());
        }

        $entry->setTrigger($trigger);
        $entry->setContent(serialize($data));
        $entry->setDeletedBy($deleted_by);
        $entry->setDeletedAt(time());

        try {
            $entry->save($conn);
        } catch (\Exception $e) {
            //Tools::log($e->getMessage());
        }
    }
}
