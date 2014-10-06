<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AxBundle\Actions\Out;

use Hanzo\Core\Tools;
use Hanzo\Model\Orders;
use Hanzo\Model\OrdersToAxQueueLog;
use Hanzo\Model\OrdersToAxQueueLogPeer;
use Hanzo\Model\OrdersToAxQueueLogQuery;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxyInterface;

/**
 * Class PheanstalkQueue
 *
 * @package Hanzo\Bundle\AxBundle
 */
class PheanstalkQueue
{
    /**
     * @var PheanstalkProxyInterface
     */
    private $pheanstalk;

    /**
     * @var null|\PropelPDO
     */
    private $dbConn = null;


    /**
     * @param PheanstalkProxyInterface $pheanstalk
     */
    public function __construct(PheanstalkProxyInterface $pheanstalk)
    {
        $this->pheanstalk = $pheanstalk;
    }


    /**
     * @param null|\PropelPDO $dbConn
     */
    public function setDBConnection($dbConn = null)
    {
        $this->dbConn = $dbConn;
    }


    /**
     * Append an order to the queue and queue log.
     * If the order is already in the queue, OrderAlreadyInQueueException is thrown.
     *
     * @param Orders $order
     * @param bool   $inEdit
     * @param int    $priority
     * @param int    $delay
     *
     * @return int
     * @throws OrderAlreadyInQueueException
     */
    public function appendSendOrder(Orders $order, $inEdit = false, $priority = \Pheanstalk_PheanstalkInterface::DEFAULT_PRIORITY, $delay = \Pheanstalk_PheanstalkInterface::DEFAULT_DELAY)
    {
        $ts = $this->isOrderInQueue($order);

        if ($ts) {
            throw new OrderAlreadyInQueueException('The order #'.$order->getId().' is already in the queue. It was added @ '.date('Y-m-d H:i:s', $ts));
        }

        $endPoint = Tools::domainKeyToEndpoint($order->getAttributes($this->dbConn)->global->domain_key);
        $data     = json_encode([
            'action'        => 'create',
            'customer_id'   => $order->getCustomersId(),
            'db_conn'       => 'pdldb'.strtolower($endPoint).'1',
            'end_point'     => $endPoint,
            'iteration'     => 0,
            'order_id'      => $order->getId(),
            'order_in_edit' => $inEdit,
        ]);

        $queueId = $this->pheanstalk->putInTube('orders2ax', $data, $priority, $delay);
        $this->addToQueueLog($order->getId(), $queueId);

        return $queueId;
    }


    /**
     * Append an delete-order to the queue and queue log.
     * If the order is already in the queue, OrderAlreadyInQueueException is thrown.
     *
     * @param Orders $order
     * @param int    $priority
     * @param int    $delay
     *
     * @return int
     * @throws OrderAlreadyInQueueException
     */
    public function appendDeleteOrder(Orders $order, $priority = \Pheanstalk_PheanstalkInterface::DEFAULT_PRIORITY, $delay = \Pheanstalk_PheanstalkInterface::DEFAULT_DELAY)
    {
        $ts = $this->isOrderInQueue($order);

        if ($ts) {
            throw new OrderAlreadyInQueueException('The order #'.$order->getId().' is already in the queue. It was added @ '.date('Y-m-d H:i:s', $ts));
        }

        if ($order->getPaymentTransactionId()) {
            $paymentId = $order->getPaymentTransactionId();
        } else {
            $attributes = $order->getAttributes($this->dbConn);
            $paymentId  = isset($attributes->payment->transact)
                ? $attributes->payment->transact
                : ''
            ;
        }

        if ($order->getEndPoint()) {
            $endPoint = $order->getEndPoint();
        } else {
            $endPoint = Tools::domainKeyToEndpoint($order->getAttributes($this->dbConn)->global->domain_key);
        }

        $data = json_encode([
            'action'      => 'delete',
            'customer_id' => $order->getCustomersId(),
            'db_conn'     => 'pdldb' . strtolower($endPoint) . '1',
            'end_point'   => $endPoint,
            'iteration'   => 0,
            'order_id'    => $order->getId(),
            'payment_id'  => $paymentId,
        ]);

        $queueId = $this->pheanstalk->putInTube('orders2ax', $data, $priority, $delay);
        $this->addToQueueLog($order->getId(), $queueId);

        return $queueId;
    }


    /**
     * Check if an order is in the queue.
     *
     * @param Orders|int $order Orders object or order id
     *
     * @return false|int         false if not in queue, timestamp of insertion if found.
     */
    public function isOrderInQueue($order)
    {
        if ($order instanceof Orders) {
            $order = $order->getId();
        }

        $ts = OrdersToAxQueueLogQuery::create()
            ->select(OrdersToAxQueueLogPeer::CREATED_AT)
            ->findOneByOrdersId($order, $this->dbConn);

        if ($ts) {
            return $ts;
        }

        return false;
    }


    /**
     * Remove an entry from the
     * @param int $orderId
     *
     * @return int
     * @throws \Exception
     */
    public function removeFromQueryLog($orderId)
    {
        return OrdersToAxQueueLogQuery::create()
            ->filterByOrdersId($orderId)
            ->delete($this->dbConn);
    }


    /**
     * Delete a job from the beanstalk queue
     *
     * @param int $jobId
     *
     * @return mixed
     */
    public function removeFromQuery($jobId)
    {
        $job = $this->pheanstalk->peek($jobId);

        if ($job) {
            return $this->pheanstalk->delete($job);
        }

        return false;
    }


    /**
     * Add to queue log.
     *
     * @param int $orderId
     * @param int $queueId
     *
     * @throws \Exception
     */
    private function addToQueueLog($orderId, $queueId)
    {
        $log = new OrdersToAxQueueLog();
        $log->setCreatedAt(time());
        $log->setOrdersId($orderId);
        $log->setQueueId($queueId);
        $log->save($this->dbConn);
    }
}
