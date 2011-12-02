<?php

namespace Hanzo\Core;

class CoreCache
{
    protected $cache = NULL;
    protected $prefix = 'hanzo.cache:';

    /**
     * setup the cache engine - here redis
     */
    public function __construct($container)
    {
        $this->cache = $container->get('snc_redis.default_client');
    }

    /**
     * check if a cache key exists
     *
     * @param mixed $key int|string accepted
     * @return boolean
     */
    public function has($key)
    {
        self::checkId($key);

        return $this->cache->exists($key);
    }

    /**
     * fetch cache entry
     *
     * @param mixed $key int|string accepted
     * @return mixed
     */
    public function get($key)
    {
        self::checkId($key);

        $data = $this->cache->get($key);

        // unserialize the cached data if needed
        if ($data && (substr($data, 0, 5) == ':[S]:')) {
            $data = unserialize(str_replace(':[S]:', $data));
        }

        return $data;
    }

    /**
     * store an entry in cache
     *
     * @param mixed $key int|string accepted
     * @param mixed $data
     * @param mixed $ttl cache time in seconds
     * @throws InvalidArgumentException
     * @return boolean
     */
    public function set($key, $data, $ttl = 3600)
    {
        self::checkId($key);

        // kick resources
        if (is_resource($data)) {
            throw new \InvalidArgumentException("We do not cache resources.", 100);
        }

        // serialize data wich is not scalar
        if (!is_scalar($data)) {
          $data = ':[S]:' . serialize($data);
        }

        // store cache and set ttl
        return $this->cache->pipeline(function($pipe) use ($key, $data, $ttl) {
            $pipe->set($key, $data);
            $pipe->ttl($key, $ttl);
        });
    }

    /**
     * expire a cache entry
     *
     * @params mixed $key
     * @return boolean
     */
    public function expire($key)
    {
        self::checkId($key);

        return $this->cache->del($key);
    }

    /**
     * creates an id from the arguments send to the method
     *
     * @param mixed
     * @return string
     */
    public function id()
    {
        return $this->prefix . implode(':', func_get_args());
    }

    /**
     * check the sanity of the keys used.
     *
     * @param mixed $key
     * @throws InvalidArgumentException
     * @return string
     */
    protected static function checkId($key)
    {
        if (empty($key) || !is_string($key) || !is_integer($key)) {
          throw new \InvalidArgumentException("Only strings are accepted as cache keys.", 200);
        }

        return $key;
    }
}
