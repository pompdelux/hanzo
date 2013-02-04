<?php

namespace Hanzo\Bundle\RedisBundle\Client;

use InvalidArgumentException;
use Hanzo\Bundle\RedisBundle\Logger\Logger;

Class Redis
{
    protected $redis = null;
    protected $logger = null;
    protected $connected = false;
    protected $parameters = [];

    protected $name;


    public function __construct($name, $environment, array $parameters = [])
    {
        $this->parameters = $parameters;
        $this->name = $name;

        $postfix = $name.'.'.$environment.':';
        if (empty($this->parameters['prefix'])) {
            $this->parameters['prefix'] = $postfix;
        } else {
            $this->parameters['prefix'] .= '.'.$postfix;
        }
    }

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function __call($name, $arguments = [])
    {
        if (!$this->connected) {
            $this->connect();
        }

        // ignore commands
        if (in_array($name, ['connect', 'pconnect'])) {
            return;
        }

        $log = true;
        switch (strtolower($name)) {
            case 'connect':
            case 'open':
            case 'pconnect':
            case 'popen':
            case 'close':
            case 'setoption':
            case 'getoption':
            case 'auth':
            case 'select':
                $log = false;
                break;
        }

        if (method_exists($this->redis, $name)) {
            $ts = microtime(true);
            $result = call_user_func_array([$this->redis, $name], $arguments);
            $ts = (microtime(true) - $ts) * 1000;

            $is_error = false;
            if ($error = $this->redis->getLastError()) {
                $this->redis->clearLastError();
                $is_error = true;
            }

            if ($log && null !== $this->logger) {
                $this->logger->logCommand($this->getCommandString($name, $arguments), $ts, $this->name, $is_error);
            }

            if ($is_error) {
                throw new RedisCommunicationException('Redis command failed: '.$error);
            }

            return $result;
        }

        throw new InvalidArgumentException('No such redis command: '.$name);

    }


    /**
     * Generate cache key
     *
     * @return string
     */
    public function generateKey()
    {
        $arguments = func_get_args();

        if (is_array($arguments[0])) {
            $arguments = $arguments[0];
        }

        return implode(':', $arguments);
    }


    /**
     * handle connection management
     *
     * @param  boolean $bail_on_error keeps track on connection retry
     * @return boolean
     * @throws RedisCommunicationException If connection fails
     */
    protected function connect($bail_on_error = false)
    {
        $this->redis = new \Redis();

        $this->connected = $this->redis->connect(
            $this->parameters['host'],
            $this->parameters['port'],
            ($this->parameters['timeout'] ?: 0)
        );

        if ($this->connected) {
            if ($this->redis->select($this->parameters['database'])) {
                $this->redis->setOption(\Redis::OPT_PREFIX, $this->parameters['prefix']);
                $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);

                return true;
            }
        } elseif (!$bail_on_error) {
            // try to re-connect, but only once.
            usleep(1000);
            $this->connect(true);
        }

        $error = $this->redis->getLastError();
        $this->redis->clearLastError();

        throw new RedisCommunicationException('Could not connect to Redis: '.$error);
    }


    /**
     * Returns a string representation of the given command including arguments
     *
     * @param string $command   A command name
     * @param array  $arguments An array of command arguments
     *
     * @return string
     */
    private function getCommandString($command, array $arguments)
    {
        $list = array();
        $this->flatten($arguments, $list);

        return mb_substr(trim(strtoupper($command) . ' ' . implode(' ', $list)), 0, 256);
    }

    /**
     * Flatten arguments to single dimension array
     *
     * @param array $arguments An array of command arguments
     * @param array $list Holder of results
     */
    private function flatten($arguments, array &$list)
    {
        foreach ($arguments as $key => $item) {
            if (!is_numeric($key)) {
                $list[] = $key;
            }

            if (is_scalar($item)) {
                $list[] = strval($item);
            } else {
                $this->flatten($item, $list);
            }
        }
    }}
