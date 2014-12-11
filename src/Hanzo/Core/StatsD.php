<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Core;

/**
 * Class StatsD
 *
 * @package Hanzo\Core
 */
class StatsD
{
    private $parameters = [
        'enabled' => false,
        'host'    => '127.0.0.1',
        'port'    => 8125
    ];

    private $data = [];

    /**
     * @param string $variabledDsn
     */
    public function __construct($variabledDsn)
    {
        if ($variabledDsn) {
            list($host, $port) = explode(':', $variabledDsn);

            $this->parameters['enabled'] = true;
            $this->parameters['host'] = $host;
            $this->parameters['port'] = $port ?: 8125;
        }
    }

    /**
     * @param string $variable
     * @param float  $time
     */
    public function timing($variable, $time)
    {
        $this->data[] = "$variable:$time|ms";
    }

    /**
     * @param string $variable
     * @param float  $value
     */
    public function gauge($variable, $value)
    {
        $this->data[] = "$variable:$value|g";
    }

    /**
     * @param string $variable
     * @param float  $value
     */
    public function measure($variable, $value)
    {
        $this->data[] = "$variable:$value|c";
    }

    /**
     * @param string $variable
     */
    public function increment($variable)
    {
        $this->data[] = "$variable:1|c";
    }

    /**
     * @param string $variable
     */
    public function decrement($variable)
    {
        $this->data[] = "$variable:-1|c";
    }

    /**
     * Flush the statsd cache to the statsd server.
     */
    public function flush()
    {
        if ((false === $this->parameters['enabled']) || empty($this->data)) {
            return;
        }

        try {
            $host = $this->parameters["host"];
            $port = $this->parameters["port"];
            $fp = fsockopen("udp://$host", $port, $errno, $errstr);

            if (! $fp) {
                return;
            }

            $level = error_reporting(0);
            foreach ($this->data as $line) {
                fwrite($fp, $line);
            }
            error_reporting($level);

        } catch (\Exception $e) {
        }
    }
}
