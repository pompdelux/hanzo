<?php

namespace Hanzo\Core;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CoreController extends Controller
{
    protected $cache;

    public function __construct()
    {
      $this->cache = new CoreCache($this->container);
    }

    public function cacheGet()
    {
        $id = implode('.', func_get_args());
    }

    public function cacheSet($data, $ttl = 60 /*, ..., ..., ...*/)
    {
        $params = func_get_args();
        $data = array_shift($params);
        $ttl  = array_shift($params);

        $id = 'hanzo.' . implode('.', $params);
    }
}
