<?php

namespace Hanzo\Bundle\RedisBundle\Client;

use Hanzo\Bundle\RedisBundle\Logger\Logger;

Class Redis
{
    /**
     * Redis instance
     * @var Redis
     */
    protected $redis = null;

    /**
     * Logger
     * @var Logger
     */
    protected $logger = null;

    /**
     * Whether or not we are connected to the redis server
     * @var boolean
     */
    protected $connected = false;

    /**
     * Redis connection parameters
     * @var array
     */
    protected $parameters = [];

    /**
     * Name/label of the connection, used for logging
     * @var string
     */
    protected $name;


    /**
     * constructor method
     *
     * @param string $name        Name of the connection
     * @param string $environment The kernel environment variable
     * @param array  $parameters  Configuration parameters
     */
    public function __construct($name, $environment, array $parameters = [])
    {

        if ($parameters['skip_env']) {
            $prefix = $name.':';
        } else {
            $prefix = $name.'.'.$environment.':';
        }

        if (empty($parameters['prefix'])) {
            $parameters['prefix'] = $prefix;
        } else {
            $parameters['prefix'] .= '.'.$prefix;
        }

        $this->name = $name;
        $this->parameters = $parameters;
    }


    public function getPrefix()
    {
        return $this->parameters['prefix'];
    }

    public function setPrefix($s)
    {
        $this->parameters['prefix'] = $s;

        if ($this->connected) {
            $this->redis->setOption(\Redis::OPT_PREFIX, $this->parameters['prefix']);
        }
    }

    /**
     * setup logging
     *
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }


    /**
     * wrap all redis calls to handle profiling
     *
     * @param  string $name      Redis method
     * @param  array  $arguments Arguments to parse on the real method
     * @return boolean
     * @throws InvalidArgumentException    If the command called does not exist
     * @throws RedisCommunicationException If the call to redis fails
     */
    public function __call($name, array $arguments = [])
    {
        if (!$this->connected) {
            $this->connect();
        }

        // ignore connect commands
        if (in_array($name, ['connect', 'pconnect'])) {
            return;
        }

        $log = true;
        switch (strtolower($name)) {
            case 'open':
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

        throw new \InvalidArgumentException('No such redis command: '.$name);
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
                // setup default options
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

        if ($this->logger) {
            $this->logger->err('Could not connect to redis server (' . $error . ')');
        }

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
        $list = [];
        foreach ($arguments as $argument) {
            $list[] = is_scalar($argument) ? $argument : '[.. complex type ..]';
        }

        return mb_substr(trim(strtoupper($command) . ' ' . implode(' ', $list)), 0, 256);
    }
}
