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
use Hanzo\Core\Tools;

/**
 * Class AxSoapClient
 * @package Hanzo\Bundle\AxBundle
 */
class AxSoapClient
{
    /**
     * @var \SoapClient
     */
    protected $client;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var bool
     */
    private $axState = false;

    /**
     * @var bool
     */
    private $logRequests = false;

    /**
     * @var ServiceLogger
     */
    private $serviceLogger;

    /**
     * @var bool
     */
    private $skipSend = false;

    /**
     * @var string
     */
    private $wsdl;

    /**
     * @param string        $wsdl
     * @param bool          $logRequests
     * @param Logger        $logger
     * @param ServiceLogger $serviceLogger
     */
    public function __construct($wsdl, $logRequests, Logger $logger, ServiceLogger $serviceLogger)
    {
        $this->wsdl            = $wsdl;
        $this->logRequests     = $logRequests;
        $this->logger          = $logger;
        $this->serviceLogger   = $serviceLogger;

        // primarily used in dev mode where ax is not available
        if (empty($wsdl)) {
            $this->skipSend = true;
        }
    }


    /**
     * Performs the actual communication with AX
     *
     * @param string $service Name of the service to call
     * @param object $request Request parameters
     *
     * @throws \Exception
     *
     * @return object.
     */
    public function send($service, $request)
    {
        if ($this->logRequests) {
            $x = print_r($request, 1);
            error_log(__METHOD__.' '.__LINE__.' '.$x);

            $x = trim(preg_replace("/ +/", ' ', str_replace(['Hanzo\\Bundle\\AxBundle\\Actions\\Out\\Services\\Mappers\\', "\n"], '', $x)));
            $this->logger->debug('Calling: '.$service, (array) $x);
        }

        if ($this->skipSend) {
            return true;
        }

        if (!$this->client) {
            if (!$this->Connect()) {
                throw new \Exception('There was an error connecting with the server! Please try again later.');
            }
        }

        try {
            $result = $this->client->{$service}($request);
        } catch (\SoapFault $e) {
            $result = $e;
        }

        $this->logAction($service);

        if ($result instanceof \Exception) {
            throw $result;
        }

        // Error code exist in $result->SyncSalesOrderResult->Status, but it
        if (isset($result->SyncSalesOrderResult->Status) && ('ERROR' === strtoupper($result->SyncSalesOrderResult->Status))) {
            $message = 'unknown error';
            if (isset($result->SyncSalesOrderResult->Message)) {
                if (is_array($result->SyncSalesOrderResult->Message)) {
                    $message = implode(' & ', $result->SyncSalesOrderResult->Message);
                }

                $message = $result->SyncSalesOrderResult->Message;
            }

            if ('unknown error' === $message) {
                Tools::log('AX comm error: '.print_r($result, 1));
            }

            throw new AxDataException($service.' sync failed, error was: '. $message);
        }

        return true;
    }


    /**
     * test and initiate ax connection
     *
     * @return boolean [description]
     */
    private function connect()
    {
        if ($this->skipSend) {
            return true;
        }

        // first we test the connection, soap has lousy timeout handeling
        $c = curl_init();
        curl_setopt_array($c, [
            CURLOPT_URL            => $this->wsdl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,  // connection
            CURLOPT_TIMEOUT        => 10, // execution timeout
        ]);

        $file = curl_exec($c);
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);
        curl_close($c);

        // ok the header send was ok, and we have file content.
        if ($status == 200 && $file) {
            $this->axState = true;
            unset($file);
        } else {
            return false;
        }

        $this->client = new \SoapClient($this->wsdl, [
            'trace'              => true,
            'exceptions'         => true,
            'connection_timeout' => 600,
        ]);

        $this->client->__setLocation(str_replace('?wsdl', '', $this->wsdl));

        return true;
    }


    /**
     * Log the request
     *
     * @param string $action
     */
    private function logAction($action)
    {
        $this->serviceLogger->plog($this->client->__getLastRequest(), ['outgoing', 'ax', 'soap', $action]);
    }
}
