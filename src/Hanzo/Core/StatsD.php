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

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class StatsD
 *
 * @package Hanzo\Core
 */
class StatsD
{
    /**
     * StatsD parameters
     *
     * @var array
     */
    private $parameters = [
        'host'    => '127.0.0.1',
        'port'    => 8125
    ];

    /**
     * Data to send to StatsD
     *
     * @var array
     */
    private $data = [];

    /**
     * StatsD key prefix
     *
     * @var string
     */
    private $prefix;

    /**
     * Name of the current route - if the request is an http(s) request
     *
     * @var string
     */
    private $routeName;

    /**
     * Whether or not the service is enabled
     *
     * @var bool
     */
    private $enabled = false;

    /**
     * Constructor
     *
     * @param string $host
     * @param int    $port
     * @param string $env
     */
    public function __construct($host, $port = 8125, $env = '')
    {
        if ($host) {
            $this->enabled = true;
            $this->parameters['host'] = $host;
            $this->parameters['port'] = $port;

            list($env, ) = explode('_', $env, 2);
            $this->prefix = $env.'.';
        }
    }

    /**
     * Add timing log, note the $time variable is in milliseconds
     *
     * @param string $variable
     * @param float  $time
     */
    public function timing($variable, $time)
    {
        $this->data[] = "{$this->prefix}{$variable}:{$time}|ms";
    }

    /**
     * Add arbitrary gauge values
     *
     * @param string $variable
     * @param float  $value
     */
    public function gauge($variable, $value)
    {
        $this->data[] = "{$this->prefix}{$variable}:{$value}|g";
    }

    /**
     *
     * @param string $variable
     * @param float  $value
     */
    public function count($variable, $value)
    {
        $this->data[] = "{$this->prefix}{$variable}:{$value}|c";
    }

    /**
     * Increment a counter by one
     *
     * @param string $variable
     */
    public function increment($variable)
    {
        $this->data[] = "{$this->prefix}{$variable}:1|c";
    }

    /**
     * Decrement a counter by one
     *
     * @param string $variable
     */
    public function decrement($variable)
    {
        $this->data[] = "{$this->prefix}{$variable}:-1|c";
    }


    /**
     * Used as a kernel.request event listener, and allows us to track buildtimes for named routes.
     *
     * @param GetResponseEvent $event
     */
    public function onCoreHTTPRequest(GetResponseEvent $event)
    {
        if ((false === $this->enabled)) {
            return;
        }

        $request = $event->getRequest();
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $route = $request->get('_route');
        if ($route && !in_array($route, ['_wdt', 'is_authenicated', 'bazinga_jstranslation_js'])) {
            $this->routeName = $route;
        }
    }


    /**
     * Flush the statsd cache to the statsd server.
     *
     * If the request is a http request, we add the buildtime for the request to the outgoing payload.
     */
    public function flush()
    {
        if (false === $this->enabled) {
            return;
        }

        if (isset($_SERVER['REQUEST_TIME_FLOAT']) && $this->routeName) {
            // register the time (in milliseconds) it took to process the request.
            $this->timing('buildtime.'.$this->routeName, number_format(((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 100), 3, '.', ''));
        }

        if (empty($this->data)) {
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
