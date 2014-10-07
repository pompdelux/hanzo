<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AxBundle\Actions\Out\Services;

use Hanzo\Bundle\AxBundle\Logger;
use Hanzo\Core\ServiceLogger;

/**
 * Class BaseService
 * @package Hanzo\Bundle\AxBundle
 */
abstract class BaseService
{
    /**
     * @var \PropelPDO
     */
    private $dbConnection;

    /**
     * @var AxSoapClient
     */
    private $axClient;

    /**
     * @var string
     */
    private $endPoint;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var ServiceLogger
     */
    protected $serviceLogger;

    /**
     * @var \stdClass
     */
    protected $data;

    /**
     * @var mixed
     */
    protected $errors;

    /**
     * Set database connection if it needs to be overridden.
     *
     * @param \PropelPDO $con
     */
    public function setDBConnection($con)
    {
        $this->dbConnection = $con;
    }


    /**
     * Get Propel connection instance
     *
     * @return \PropelPDO
     */
    protected function getDBConnection()
    {
        return $this->dbConnection;
    }


    /**
     * Set AX client instance
     *
     * @param AxSoapClient $axClient
     */
    public function setAxClient(AxSoapClient $axClient)
    {
        $this->axClient = $axClient;
    }


    /**
     * Get AX client instance
     *
     * @return AxSoapClient
     */
    protected function getAxClient()
    {
        return $this->axClient;
    }


    /**
     * Set service logger instance
     *
     * @param ServiceLogger $serviceLogger
     */
    public function setServiceLogger(ServiceLogger $serviceLogger)
    {
        $this->serviceLogger = $serviceLogger;
    }


    /**
     * Set logger instance
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }


    /**
     * AX endpoint, used to send customers to the correct AX financial account.
     *
     * @param string $e
     */
    public function setEndPoint($e = 'DK')
    {
        $this->endPoint = $e;
    }

    /**
     * Retrive any errors catched
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get current AX endpoint
     * @return string
     */
    protected function getEndPoint()
    {
        return $this->endPoint;
    }


    /**
     * Send the actual object to AX.
     *
     * Errors are logged and true|false returned
     *
     * @param string $name Name of the AX method to call.
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function send($name = null)
    {
        if (empty($name)) {
            $name = trim(strrchr(get_class($this), '\\'), '\\');
        }

        if (!$this->getEndPoint()) {
            throw new \InvalidArgumentException("You must set end point via setEndPoint() !");
        }

        // be sure to populate before validation
        $data = $this->get();

        // call validation routine, note these must be implemented in extending classes.
        $this->validate();

        try {
            $this->axClient->send($name, $data);
        } catch (\Exception $e) {
            $this->logger->critical('An error occured in '.$name.' sync! Error message: "'.$e->getMessage().'"');
            $this->errors = $e;

            return false;
        }

        return true;
    }


    /**
     * The data object as it's send to AX.
     *
     * @return \stdClass
     */
    abstract public function get();


    /**
     * Validation of the current service.
     * This method is called prior to send() call.
     *
     * If implemented errors must be thrown as exceptions
     *
     * @throws \Exception
     */
    protected function validate()
    {
    }
}
