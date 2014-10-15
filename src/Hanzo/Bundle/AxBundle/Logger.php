<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\AxBundle;

use Hanzo\Model\OrdersSyncLog;
use Psr\Log\LoggerInterface;

/**
 * Class Logger, intended to handle all logging for ax communication.
 *
 * @package Hanzo\Bundle\AxBundle
 */
class Logger implements LoggerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \PDO
     */
    private $connection;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Set needed DB connection
     *
     * @param \PDO|\PropelPDO $connection
     */
    public function setDBConnection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Log sync state to DB
     *
     * @param int    $orderId
     * @param string $state
     * @param mixed  $data
     * @param string $comment
     *
     * @throws \InvalidArgumentException
     */
    public function write($orderId, $state = 'ok', $data = [], $comment = '')
    {
        if (empty($this->connection)) {
            throw new \InvalidArgumentException("You must set DB connection via setDBConnection() !");
        }

        $entry = new OrdersSyncLog();
        $entry->setOrdersId($orderId);
        $entry->setCreatedAt('now');
        $entry->setState($state);
        $entry->setContent(serialize($data));

        if ($comment) {
            $entry->setComment($comment);
        }

        try {
            $entry->save($this->connection);
        } catch (\Exception $e) {
            $this->logger->error('Could not write "orders_sync_log" entry, error was: '.$e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function emergency($message, array $context = [])
    {
        $this->logger->emergency($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function alert($message, array $context = [])
    {
        $this->logger->alert($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function critical($message, array $context = [])
    {
        $this->logger->critical($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, array $context = [])
    {
        $this->logger->error($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, array $context = [])
    {
        $this->logger->warning($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function notice($message, array $context = [])
    {
        $this->logger->notice($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function info($message, array $context = [])
    {
        $this->logger->info($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, array $context = [])
    {
        $this->logger->debug($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = [])
    {
        $this->logger->log($level, $message, $context);
    }

}
