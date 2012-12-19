<?php

namespace Hanzo\Bundle\VarnishBundle;

use VarnishAdmin;
use VarnishException;

use Hanzo\Core\Tools;

class Varnish
{
    protected $varnish;
    protected $connected;

    public function __construct($host, $port = 6082, $secret, $timeout = 300)
    {
        if (class_exists('VarnishAdmin')) {
            $args = array(
                VARNISH_CONFIG_HOST => $host,
                VARNISH_CONFIG_PORT =>  $port,
                VARNISH_CONFIG_SECRET => $secret,
                VARNISH_CONFIG_TIMEOUT => $timeout,
            );

            $this->varnish = new VarnishAdmin($args);
        }
    }

    public function ban($regex)
    {
        if (!$this->varnish) {
            return true;
        }

        $status = $this->varnish->ban('req.url ~ "'.$regex.'"');

        if (VARNISH_STATUS_OK !== $status) {
            throw new VarnishException("Ban method returned $status status\n");
        }

        return true;
    }

    public function banUrl($regex)
    {
        if (!$this->varnish) {
            return true;
        }

        $status = $this->varnish->banUrl('req.url ~ "'.$regex.'"');

        if (VARNISH_STATUS_OK !== $status) {
            throw new VarnishException("BanUrl method returned $status status\n");
        }

        return true;
    }

    protected function connect()
    {
        if ($this->connected) {
            return true;
        }

        $varnish->connect();
        $this->connected = $varnish->auth();
    }
}
