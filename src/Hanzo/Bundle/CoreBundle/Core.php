<?php
/*
 * This file is part of the hanzo package.
 *
 * (c) Ulrik Nielsen <un@bellcom.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hanzo\Bundle\CoreBundle;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CoreService
 * @package Hanzo\Bundle\CoreBundle
 */
class Core
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * @var ContainerInterface
     */
    private $container;


    /**
     * @param array $parameters
     * @param ContainerInterface $container
     */
    public function __construct(array $parameters = [], ContainerInterface $container)
    {
        $this->parameters = $parameters;
        $this->container = $container;
error_log('x');
    }

    /**
     * @param  $key
     * @return null
     */
    public function getParameter($key)
    {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }

        return null;
    }
}
