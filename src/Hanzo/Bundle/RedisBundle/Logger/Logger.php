<?php

namespace Hanzo\Bundle\RedisBundle\Logger;

use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * Logger
 */
class Logger
{
    protected $logger;
    protected $command_count = 0;
    protected $commands = array();

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger A LoggerInterface instance
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * Logs a command
     *
     * @param string      $command    Redis command
     * @param float       $duration   Duration in milliseconds
     * @param string      $connection Connection alias
     * @param bool|string $error      Error message or false if command was successful
     */
    public function logCommand($command, $duration, $connection, $error = false)
    {
        ++$this->command_count;

        if (null !== $this->logger) {
            $this->commands[] = array('cmd' => $command, 'executionMS' => $duration, 'conn' => $connection, 'error' => $error);
            if ($error) {
                $this->logger->err('Command "' . $command . '" failed (' . $error . ')');
            } else {
                $this->logger->debug('Executing command "' . $command . '"');
            }
        }
    }

    /**
     * Returns the number of logged commands.
     *
     * @return integer
     */
    public function getNbCommands()
    {
        return $this->command_count;
    }

    /**
     * Returns an array of the logged commands.
     *
     * @return array
     */
    public function getCommands()
    {
        return $this->commands;
    }
}
