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

use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;

/**
 * Class ServiceLogger
 *
 * @package Hanzo\Core
 * @method emergency(string $message, array $context = array())
 * @method alert(string $message, array $context = array())
 * @method critical(string $message, array $context = array())
 * @method error(string $message, array $context = array())
 * @method warning(string $message, array $context = array())
 * @method notice(string $message, array $context = array())
 * @method info(string $message, array $context = array())
 * @method debug(string $message, array $context = array())
 * @method log(mixed $level, string $message, array $context = array())
 */
class ServiceLogger
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;


    /**
     * Construct
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    /**
     * Proxy method, forwards all calls to the logger instance.
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function __call($method, array $args = [])
    {
        if (method_exists($this->logger, $method)) {
            return call_user_func_array([$this->logger, $method], $args);
        }

        throw new InvalidArgumentException($method.' not supported by Psr\Log\LoggerInterface');
    }


    /**
     * Send mixed data to the logger, if not a string the data is run through print_r first.
     * Unlike the native Logger methods, plog() always loggs with type=info
     *
     * @param  mixed $data
     * @param  array $context
     * @return mixed
     */
    public function plog($data, $context = [])
    {
        // flatten structure into one line, this is better than the alternative, but not ideal.
        $new = '';
        foreach (explode("\n", print_r($data, 1)) as $line) {
            $new .= trim($line) . ' ';
        }

        return $this->logger->info(trim($new), $context);
    }
}
