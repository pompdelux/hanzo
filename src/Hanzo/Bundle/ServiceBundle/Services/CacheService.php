<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\CmsQuery;

use Hanzo\Bundle\RedisBundle\Client\Redis;

/**
 * Class CacheService
 *
 * @package Hanzo\Bundle\ServiceBundle\Services
 */
class CacheService
{
    protected $redis;
    protected $settings;

    /**
     * @param array $parameters
     * @param array $settings
     */
    public function __construct($parameters, $settings)
    {
        $this->redis    = $parameters[0];
        $this->settings = $settings;

        if (!$this->redis instanceof Redis) {
            throw new \InvalidArgumentException('Predis\Client expected as first parameter.');
        }
    }

    /**
     * clear symfony's file cache
     *
     * @return array of status messages from the different servers.
     */
    public function clearFileCache()
    {
        set_time_limit(0);

        // note, this is not optimal, but easy tho...
        // TODO populate list from the settings table
        $servers = [
            $_SERVER['HTTP_HOST'],
        ];

        $status = [];
        foreach ($servers as $server) {
            $status[$server] = file_get_contents('http://'.$server.'/cc.php?run=1');
        }

        return $status;
    }

    /**
     * This will clear the redis cache.
     *
     * @return boolean predis command state.
     */
    public function clearRedisCache()
    {
        return (bool) $this->redis->flushdb();
    }
}
