<?php

namespace Hanzo\Bundle\ServiceBundle\Services;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

class AxService
{
    protected $parameters;
    protected $settings;

    protected $wsdl;
    protected $client;
    protected $ax_state = false;

    public function __construct($parameters, $settings)
    {
        $this->parameters = $parameters;
        $this->settings = $settings;

        $this->wsdl = 'http://'.$settings['host'].'/DynamicsAxServices.asmx?wsdl';
    }


    /**
     * trigger ax stock sync
     *
     * @param  string $endpoint DK/NO/SE/...
     * @return mixed true on success, false or \SoapFault on error
     */
    public function triggerStockSync($endpoint)
    {
        if (!$this->client) {
            if (!$this->Connect()) {
                // bail out
                return false;
            }
        }

        $data = new stdClass();
        $data->endpointDomain = $endpoint;
        try {
            $this->client->SyncInventory($data);
        } catch (\SoapFault $e) {
            return $e;
        }

        return true;
    }


    /**
     * test and initiate ax connection
     *
     * @return boolean [description]
     */
    protected function Connect()
    {
        // first we test the connection, soap has lousy timeout handeling
        $c = curl_init();
        curl_setopt_array($c, array(
            CURLOPT_URL            => $this->wsdl,
            CURLOPT_CONNECTTIMEOUT => 5, // connection
            CURLOPT_TIMEOUT        => 6, // execution timeout
            CURLOPT_RETURNTRANSFER => true,
        ));

        $file = curl_exec($c);
        $status = curl_getinfo($c,  CURLINFO_HTTP_CODE);
        curl_close($c);

        // ok the header send was ok, and we have file content.
        if ($status == 200 && $file) {
            $this->ax_state = true;
            unset($file);
        } else {
            return false;
        }

        $this->client = new SoapClient($wsdl, array(
          'trace'      => true,
          'exceptions' => true,
        ));
        $this->client->__setLocation(str_replace('?wsdl', '', $wsdl));

        return true;
    }
}
