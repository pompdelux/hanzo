<?php

namespace Hanzo\Bundle\ConsignorBundle;

use Guzzle\Service\Client;
use Symfony\Bridge\Monolog\Logger;

class Consignor
{
    /**
     * @var \Guzzle\Service\Client
     */
    private $guzzle;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    private $logger;

    /**
     * @var array
     */
    private $options;


    /**
     * @param Client $guzzle_client
     * @param Logger $logger
     */
    public function __construct(Client $guzzle_client, Logger $logger)
    {
        $this->guzzle = $guzzle_client;
        $this->logger = $logger;
    }


    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }


    /**
     * @param $key
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getOption($key)
    {
        if (isset($this->options[$key])) {
            return $this->options[$key];
        }

        throw new \InvalidArgumentException("The key: '{$key}' is not a valid option key.");
    }


    /**
     * @return Client
     */
    public function getGuzzleClient()
    {
        return $this->guzzle;
    }
}
