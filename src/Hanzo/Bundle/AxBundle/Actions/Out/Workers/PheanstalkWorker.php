<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AxBundle\Actions\Out\Workers;

use Hanzo\Bundle\AxBundle\Actions\Out\AxServiceWrapper;
use Hanzo\Bundle\AxBundle\Logger;
use Hanzo\Bundle\CheckoutBundle\SendOrderConfirmationMail;
use Hanzo\Core\Tools;
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\OrdersQuery;
use Hanzo\Model\OrdersToAxQueueLog;
use Hanzo\Model\OrdersToAxQueueLogQuery;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxy;

class PheanstalkWorker
{
    private $pheanstalkProxy;
    private $serviceWrapper;
    private $orderConfirmationMailer;
    private $logger;

    /**
     * @var null|\PropelPDO
     */
    private $dbConn = null;

    /**
     * @param PheanstalkProxy           $pheanstalkProxy
     * @param AxServiceWrapper          $serviceWrapper
     * @param SendOrderConfirmationMail $orderConfirmationMailer
     * @param Logger                    $logger
     */
    public function __construct(PheanstalkProxy $pheanstalkProxy, AxServiceWrapper $serviceWrapper, SendOrderConfirmationMail $orderConfirmationMailer, Logger $logger)
    {
        $this->pheanstalkProxy         = $pheanstalkProxy;
        $this->serviceWrapper          = $serviceWrapper;
        $this->orderConfirmationMailer = $orderConfirmationMailer;
        $this->logger                  = $logger;
    }


    /**
     * Send customer and order to AX or fail the jobs.
     *
     * @param  array $jobData [
     *     'order_id'      => (int)
     *     'order_in_edit' => (bool),
     *     'customer_id'   => (int),
     *     'iteration'     => (int),
     *     'end_point'     => (string),
     *     'db_conn'       => (string),
     * ]
     * @return bool
     * @throws \BuildException
     */
    public function send(array $jobData)
    {
        $jobData['iteration'] += 1;
        $this->dbConn = \Propel::getConnection($jobData['db_conn']);
        $this->logger->setDBConnection($this->dbConn);

        if ($jobData['iteration'] > 5) {
            $comment = 'PheanstalkWorker tried to syncronize the order #'.$jobData['order_id'].' '.($jobData['iteration'] -1).' times in the last ~10 minutes - we give up!';

            $this->logger->write($jobData['order_id'], 'failed', ['action' => $jobData['action']], $comment);
            $this->logger->error($comment);
            $this->removeFromQueueLog($jobData['order_id']);

            return false;
        }

        $customer = CustomersQuery::create()
            ->findOneById($jobData['customer_id'], $this->dbConn)
        ;

        if (!$this->serviceWrapper->SyncCustomer($customer, false, $this->dbConn)) {
            return $this->reQueue($jobData, 'SyncCustomer');
        }

        $order = OrdersQuery::create()
            ->findOneById($jobData['order_id'], $this->dbConn)
        ;

        try {
            $orderSyncState = $this->serviceWrapper->SyncSalesOrder($order, false, $this->dbConn, $jobData['order_in_edit']);
        } catch (\Exception $e) {
            $this->logger->write($jobData['order_id'], 'failed', ['action' => $jobData['action']], 'Syncronization halted: '.$e->getMessage());
            $this->removeFromQueueLog($jobData['order_id']);

            return false;
        }

        if (false === $orderSyncState) {
            return $this->reQueue($jobData, 'SyncSalesOrder');
        }

        // send order confirmation
        $this->orderConfirmationMailer->build($order);
        $this->orderConfirmationMailer->send();

        $this->logger->write($jobData['order_id'], 'ok', ['action' => $jobData['action']]);
        $this->removeFromQueueLog($jobData['order_id']);

        return true;
    }


    /**
     * Delete order in ax.
     *
     * @param array $jobData
     * @return bool
     */
    public function delete(array $jobData)
    {
        $jobData['iteration'] += 1;
        $this->dbConn = \Propel::getConnection($jobData['db_conn']);
        $this->logger->setDBConnection($this->dbConn);

        if ($jobData['iteration'] > 5) {
            $comment = 'PheanstalkWorker tried to delete the order #'.$jobData['order_id'].' '.$jobData['iteration'].' times in the last ~10 minutes - we give up!';

            $this->logger->write($jobData['order_id'], 'failed', [], $comment);
            $this->logger->error($comment);
            $this->removeFromQueueLog($jobData['order_id']);

            return false;
        }

        $order = OrdersQuery::create()
            ->findOneById($jobData['order_id'], $this->dbConn)
        ;

        try {
            $orderSyncState = $this->serviceWrapper->SyncDeleteSalesOrder($order, false, $this->dbConn);
        } catch (\Exception $e) {
            Tools::log('PheanstalkWorker::delete() Syncronization halted: '.$e->getMessage());
            $this->removeFromQueueLog($jobData['order_id']);

            return false;
        }

        if (false === $orderSyncState) {
            return $this->reQueue($jobData, 'SyncDeleteSalesOrder');
        }

        $this->removeFromQueueLog($jobData['order_id']);

        return true;
    }


    /**
     * Re-queue a job.
     *
     * @param  $jobData
     * @param  $type
     * @return bool
     */
    private function reQueue($jobData, $type)
    {
        $seconds = $jobData['iteration'] * 45;
        $this->logger->info('PheanstalkWorker Job "'.$type.'" failed, scheduling for re-run in '.$seconds.' seconds.');
        $queue_id = $this->pheanstalkProxy->putInTube('orders2ax',  json_encode($jobData), \Pheanstalk_PheanstalkInterface::DEFAULT_PRIORITY, $seconds);

        // bump timestamp and queue id in queue log
        $this->dbConn->query("
            UPDATE
                orders_to_ax_queue_log
            SET
                queue_id = ".(int) $queue_id.",
                iteration = iteration + 1
            WHERE
                orders_id = ".(int) $jobData['order_id']
        );

        return true;
    }

    /**
     * @param  int $order_id
     * @return int
     * @throws \Exception
     */
    private function removeFromQueueLog($order_id)
    {
        return OrdersToAxQueueLogQuery::create()
            ->filterByOrdersId($order_id)
            ->delete($this->dbConn);
    }
}
