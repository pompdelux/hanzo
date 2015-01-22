<?php

namespace Hanzo\Bundle\MunerisBundle\Services;

use Guzzle\Service\Client as GuzzleClient;
use Pompdelux\PHPRedisBundle\Client\PHPRedis;
use Symfony\Bridge\Monolog\Logger;

class MaxMind
{
    protected $container;
    protected $guzzle;
    protected $redis;
    protected $logger;

    public function __construct($container, GuzzleClient $guzzle, PHPRedis $redis, Logger $logger)
    {
        $this->container = $container;
        $this->guzzle    = $guzzle;
        $this->redis     = $redis;
        $this->logger    = $logger;
    }


    public function lookup($ip = '')
    {
        if (empty($ip)) {
            $ip = $this->container->get('request')->getClientIp();
        }
        // Local host, internal and IP used by docker
        if (preg_match('/^(127.0.0.|192.168.|0.0.|172.17.)/', $ip)) {
            $ip = '90.185.206.100';
        }

        if (substr_count($ip, '.')) {
            $ip = ip2long($ip);
        }

        if ($response = $this->getCached($ip)) {
            return $response;
        }

        $request  = $this->guzzle->get('/mm/cities/'.$ip);
        $request->setHeader('Accept', 'application/json');
        $response = $request->send();
        $data     = json_decode($response->getBody());

        $this->setCached($ip, $data);

        return $data;
    }


    protected function setCached($ip, $data)
    {
        $key = $this->redis->generateKey('geocache');
        $this->redis->hset($key, $ip, $data);
        $this->redis->expire($key, 604800);
    }


    protected function getCached($ip)
    {
        $key  = $this->redis->generateKey('geocache');
        $data = $this->redis->hget($key, $ip);

        if ($data) {
            return $data;
        }

        return false;
    }
}
