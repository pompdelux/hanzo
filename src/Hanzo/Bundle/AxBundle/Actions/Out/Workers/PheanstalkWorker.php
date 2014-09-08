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
use Hanzo\Model\CustomersQuery;
use Hanzo\Model\OrdersQuery;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxy;

class PheanstalkWorker
{
    private $pheanstalkProxy;
    private $serviceWrapper;
    private $orderConfirmationMailer;
    private $logger;
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
            $comment = 'PheanstalkWorker tried to syncronize the order #'.$jobData['order_id'].' '.$jobData['iteration'].' times in the last ~10 minutes - we give up!';

            $this->logger->write($jobData['order_id'], 'failed', [], $comment);
            $this->logger->error($comment);

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
            $this->logger->write($jobData['order_id'], 'failed', [], 'Syncronization halted: '.$e->getMessage());

            return false;
        }

        if (false === $orderSyncState) {
            return $this->reQueue($jobData, 'SyncSalesOrder');
        }

        // send order confirmation
        $this->orderConfirmationMailer->build($order);
        $this->orderConfirmationMailer->send();

        $this->logger->write($jobData['order_id'], 'ok');

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
        $this->pheanstalkProxy->putInTube('orders2ax',  json_encode($jobData), \Pheanstalk_PheanstalkInterface::DEFAULT_PRIORITY, $seconds);

        return true;
    }

}
