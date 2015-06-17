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
use Hanzo\Bundle\AxBundle\Actions\Out\Services\AxDataException;
use Hanzo\Bundle\AxBundle\Logger;
use Hanzo\Model\CustomersQuery;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxy;

/**
 * Class PheanstalkDebitorWorker
 *
 * @package Hanzo\Bundle\AxBundle\Actions\Out
 */
class PheanstalkDebitorWorker
{
    private $pheanstalkProxy;
    private $serviceWrapper;
    private $logger;

    /**
     * @var null|\PropelPDO
     */
    private $dbConn = null;

    /**
     * @param PheanstalkProxy           $pheanstalkProxy
     * @param AxServiceWrapper          $serviceWrapper
     * @param Logger                    $logger
     */
    public function __construct(PheanstalkProxy $pheanstalkProxy, AxServiceWrapper $serviceWrapper, Logger $logger)
    {
        $this->pheanstalkProxy         = $pheanstalkProxy;
        $this->serviceWrapper          = $serviceWrapper;
        $this->logger                  = $logger;
    }


    /**
     * Send customer to AX or fail the jobs.
     *
     * @param array $jobData [
     *     'customer_id'   => (int)
     *     'iteration'     => (int),
     *     'end_point'     => (string),
     *     'db_conn'       => (string),
     * ]
     *
     * @return bool
     * @throws \BuildException
     */
    public function send(array $jobData)
    {
        $jobData['iteration'] += 1;
        $this->dbConn = \Propel::getConnection($jobData['db_conn']);
        $this->logger->setDBConnection($this->dbConn);

        if ($jobData['iteration'] > 5) {
            $this->logger->error('PheanstalkDebitorWorker tried to syncronize the debitor #'.$jobData['customer_id'].' '.($jobData['iteration'] -1).' times in the last ~10 minutes - we give up!');

            return false;
        }

        $customer = CustomersQuery::create()->findOneById($jobData['customer_id'], $this->dbConn);

        if (!$this->serviceWrapper->SyncCustomer($customer, false, $this->dbConn)) {
            $error = $this->serviceWrapper->getErrors();
            if ($error instanceof AxDataException) {
                $this->logger->error('PheanstalkDebitorWorker tried to syncronize the debitor #'.$jobData['customer_id'].' - but got an error - Syncronization halted: '.$error->getMessage());

                return false;
            }

            return $this->reQueue($jobData, 'SyncCustomer');
        }

        return true;
    }


    /**
     * Re-queue a job.
     *
     * @param array  $jobData
     * @param string $type
     *
     * @return bool
     */
    private function reQueue($jobData, $type)
    {
        $seconds = $jobData['iteration'] * 45;
        $this->logger->info('PheanstalkDebitorWorker Job "'.$type.'" failed, scheduling for re-run in '.$seconds.' seconds.');
        $this->pheanstalkProxy->putInTube('debitor2ax', json_encode($jobData), \Pheanstalk_PheanstalkInterface::DEFAULT_PRIORITY, $seconds);

        return true;
    }
}
