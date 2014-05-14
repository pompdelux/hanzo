<?php

namespace Hanzo\Bundle\VarnishBundle;

use VarnishAdmin;
use VarnishException;

use Hanzo\Core\Tools;

class Varnish
{
    protected $varnish = null;
    protected $connected = false;
    protected $status_map = [];

    /**
     * setup varnish settings
     *
     * @param string  $host    the ip address or hostname of the varnish server
     * @param integer $port    port number
     * @param string  $secret  secret, must be set or the extension will not connect properly
     * @param integer $timeout connection timeout
     */
    public function __construct($host, $port = 6082, $secret, $timeout = 300)
    {
        // if the pecl varnish module is not loaded, we skip setup
        if (class_exists('VarnishAdmin')) {
            $this->status_map = [
                VARNISH_STATUS_SYNTAX  => 'VARNISH_STATUS_SYNTAX',
                VARNISH_STATUS_UNKNOWN => 'VARNISH_STATUS_UNKNOWN',
                VARNISH_STATUS_UNIMPL  => 'VARNISH_STATUS_UNIMPL',
                VARNISH_STATUS_TOOFEW  => 'VARNISH_STATUS_TOOFEW',
                VARNISH_STATUS_TOOMANY => 'VARNISH_STATUS_TOOMANY',
                VARNISH_STATUS_PARAM   => 'VARNISH_STATUS_PARAM',
                VARNISH_STATUS_AUTH    => 'VARNISH_STATUS_AUTH',
                VARNISH_STATUS_OK      => 'VARNISH_STATUS_OK',
                VARNISH_STATUS_CANT    => 'VARNISH_STATUS_CANT',
                VARNISH_STATUS_COMMS   => 'VARNISH_STATUS_COMMS',
                VARNISH_STATUS_CLOSE   => 'VARNISH_STATUS_CLOSE',
            ];

            $args = array(
                VARNISH_CONFIG_HOST    => $host,
                VARNISH_CONFIG_PORT    => $port,
                VARNISH_CONFIG_SECRET  => $secret,
                VARNISH_CONFIG_TIMEOUT => $timeout,
            );

            $this->varnish = new VarnishAdmin($args);
        }
    }


    /**
     * send ban requests, remember this must include ex: "req.url ~" or similar
     *
     * @param  string $request  full ban request, this must include either req.url, req.host, both or similar.
     * @return true             if varnish returns success
     * @throws VarnishException on error
     */
    public function ban($request)
    {
        if (!$this->connect()) {
            return true;
        }

        $status = $this->varnish->ban($request);

        if (VARNISH_STATUS_OK !== $status) {
            if (isset($this->status_map[$status])) {
                $status = $this->status_map[$status];
            }

            throw new VarnishException("Ban method returned status: '{$status}' request send: '{$request}' (".__LINE__.")");
        }

        return true;
    }

    /**
     * send banUrl requests
     *
     * @param  string $regex    url to expire as a regex or url
     * @return true             if varnish returns success
     * @throws VarnishException on error
     */
    public function banUrl($regex)
    {
        if (!$this->connect()) {
            return true;
        }

        $status = $this->varnish->banUrl($regex);

        if (VARNISH_STATUS_OK !== $status) {
            if (isset($this->status_map[$status])) {
                $status = $this->status_map[$status];
            }

            throw new VarnishException("Ban method returned status: '{$status}' regex send: '{$regex}' (".__LINE__.")");
        }

        return true;
    }

    /**
     * handle varnish connections
     *
     * @return boolean true if connected and authorized
     */
    protected function connect()
    {
        if ($this->connected) {
            return true;
        }

        // no varnish module, we quit.
        if (!$this->varnish) {
            return false;
        }

        if (!$this->varnish->connect()) {
            Tools::log('Could not connect to varnish....');
            return false;
        }

        $this->connected = $this->varnish->auth();

        return $this->connected;
    }
}
