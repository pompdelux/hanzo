<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Pompdelux\PHPRedisBundle\Client\PHPRedis;

class CacheService
{
    protected $redis;
    protected $settings;

    public function __construct($parameters, $settings)
    {
        $this->redis = $parameters[0];
        $this->settings = $settings;

        if (!$this->redis instanceof PHPRedis) {
            throw new \InvalidArgumentException('PHPRedis\Client expected as first parameter.');
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
        $servers = array(
            $_SERVER['HTTP_HOST'],
        );

        $status = array();
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
